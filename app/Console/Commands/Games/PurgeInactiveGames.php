<?php

namespace App\Console\Commands\Games;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Optional clean-up step after the catalogue trim.
 *
 * The trim migration only switches games off. Once the owner is happy with the
 * lobby this command deletes the switched-off rows (and their favorites, likes,
 * reviews and category links) so the admin Games list is small again.
 * Bet history in `orders` references games by code/name, so it is untouched.
 */
class PurgeInactiveGames extends Command
{
    protected $signature = 'catalogue:purge {--force : Run without the confirmation prompt}';

    protected $description = 'Permanently delete games that were switched off by the catalogue trim';

    public function handle(): int
    {
        $inactive = DB::table('games')->where('status', '0');
        $count    = $inactive->count();

        if ($count === 0) {
            $this->info('No inactive games to purge.');
            return self::SUCCESS;
        }

        $this->warn("{$count} inactive games will be deleted permanently.");
        if (!$this->option('force') && !$this->confirm('Continue?')) {
            return self::SUCCESS;
        }

        $ids = $inactive->pluck('id');

        foreach ($ids->chunk(500) as $chunk) {
            $chunk = $chunk->all();
            DB::table('category_game')->whereIn('game_id', $chunk)->delete();
            DB::table('game_favorites')->whereIn('game_id', $chunk)->delete();
            DB::table('game_likes')->whereIn('game_id', $chunk)->delete();
            DB::table('game_reviews')->whereIn('game_id', $chunk)->delete();
            DB::table('games')->whereIn('id', $chunk)->delete();
        }

        DB::table('providers')
            ->where('status', 0)
            ->whereNotIn('id', DB::table('games')->distinct()->pluck('provider_id'))
            ->delete();

        $this->info("Deleted {$count} games. Remaining: " . DB::table('games')->count());

        return self::SUCCESS;
    }
}
