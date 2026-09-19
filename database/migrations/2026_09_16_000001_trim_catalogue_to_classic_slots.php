<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Slims the lobby down to the 12 locally hosted PG Soft classic slots.
 *
 * Nothing is deleted from `games`: every other title is switched off
 * (status = 0) so bet history, favorites, likes and missions that point at
 * those rows keep working. The previous flags are copied to
 * `games_catalogue_backup` so the change can be rolled back with
 * `php artisan migrate:rollback`.
 */
return new class extends Migration
{
    /**
     * game_code of the titles that stay live. These are the "source"
     * distribution games shipped in public/originals and served by
     * App\Http\Controllers\Games\*Controller, so they need no provider API key.
     */
    public const KEEP = [
        'fortunetiger',
        'fortuneox',
        'fortunemouse',
        'fortunepanda',
        'phoenixrises',
        'queenofbounty',
        'treasuresofaztec',
        'bikiniparadise',
        'hoodvswoolf',
        'jackfrost',
        'songkranparty',
        'fortunerabbit',
    ];

    public function up(): void
    {
        if (!Schema::hasTable('games_catalogue_backup')) {
            Schema::create('games_catalogue_backup', function (Blueprint $table) {
                $table->unsignedBigInteger('game_id')->primary();
                $table->string('status');
                $table->tinyInteger('is_featured')->default(0);
                $table->tinyInteger('show_home')->default(0);
                $table->tinyInteger('only_demo')->default(0);
                $table->timestamp('created_at')->nullable();
            });
        }

        DB::transaction(function () {
            // 1. snapshot the current flags once
            if (DB::table('games_catalogue_backup')->count() === 0) {
                DB::table('games')
                    ->select('id', 'status', 'is_featured', 'show_home', 'only_demo')
                    ->orderBy('id')
                    ->chunk(500, function ($rows) {
                        DB::table('games_catalogue_backup')->insert(
                            $rows->map(fn ($r) => [
                                'game_id'     => $r->id,
                                'status'      => (string) $r->status,
                                'is_featured' => (int) $r->is_featured,
                                'show_home'   => (int) $r->show_home,
                                'only_demo'   => (int) $r->only_demo,
                                'created_at'  => now(),
                            ])->all()
                        );
                    });
            }

            // 2. switch everything off ...
            DB::table('games')->update([
                'status'      => '0',
                'is_featured' => 0,
                'show_home'   => 0,
            ]);

            // 3. ... and the 12 classic slots back on
            DB::table('games')
                ->where('distribution', 'source')
                ->whereIn('game_code', self::KEEP)
                ->update([
                    'status'      => '1',
                    'is_featured' => 1,
                    'show_home'   => 1,
                    'only_demo'   => 0,
                    'updated_at'  => now(),
                ]);

            // the shipped dump has a broken accent in this title
            DB::table('games')->where('game_code', 'jackfrost')->update(['game_name' => "Jack Frost's Winter", 'description' => "Jack Frost's Winter"]);

            $kept = DB::table('games')
                ->where('distribution', 'source')
                ->whereIn('game_code', self::KEEP)
                ->get(['id', 'provider_id']);

            // 4. only the provider that owns the kept games stays visible
            DB::table('providers')->update(['status' => 0]);
            DB::table('providers')->whereIn('id', $kept->pluck('provider_id')->unique()->all())->update(['status' => 1]);

            // 5. lobby categories: keep "All" and "Slots", drop cards / live / popular / roulette
            $keepSlugs = ['todos', 'slots'];
            $dropIds   = DB::table('categories')->whereNotIn('slug', $keepSlugs)->pluck('id');
            DB::table('category_game')->whereIn('category_id', $dropIds)->delete();
            DB::table('categories')->whereIn('id', $dropIds)->delete();

            DB::table('categories')->where('slug', 'todos')->update(['name' => 'All', 'description' => 'All Games']);
            DB::table('categories')->where('slug', 'slots')->update(['name' => 'Slots', 'description' => 'Classic Slots']);

            // 6. every kept game is attached to both remaining categories
            $categoryIds = DB::table('categories')->whereIn('slug', $keepSlugs)->pluck('id');
            $gameIds     = $kept->pluck('id');

            DB::table('category_game')->whereNotIn('game_id', $gameIds)->delete();
            foreach ($categoryIds as $categoryId) {
                foreach ($gameIds as $gameId) {
                    $exists = DB::table('category_game')
                        ->where('category_id', $categoryId)
                        ->where('game_id', $gameId)
                        ->exists();

                    if (!$exists) {
                        DB::table('category_game')->insert(['category_id' => $categoryId, 'game_id' => $gameId]);
                    }
                }
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('games_catalogue_backup')) {
            return;
        }

        DB::table('games_catalogue_backup')->orderBy('game_id')->chunk(500, function ($rows) {
            foreach ($rows as $row) {
                DB::table('games')->where('id', $row->game_id)->update([
                    'status'      => $row->status,
                    'is_featured' => $row->is_featured,
                    'show_home'   => $row->show_home,
                    'only_demo'   => $row->only_demo,
                ]);
            }
        });

        DB::table('providers')->update(['status' => 1]);
        Schema::drop('games_catalogue_backup');
    }
};
