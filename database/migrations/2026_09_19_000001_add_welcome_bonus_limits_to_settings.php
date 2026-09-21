<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * The welcome bonus was a bare percentage with nothing to stop it: a 100%
 * offer paid 100% of whatever was deposited. The published terms cap it, set a
 * minimum qualifying deposit, cap the bet while the bonus is live and give the
 * bonus a life span, so those four numbers belong in the settings rather than
 * only in the wording.
 */
return new class extends Migration
{
    private const COLUMNS = [
        'bonus_max'         => ['decimal', 'The most the welcome bonus can ever pay, in site currency. 0 means no cap.'],
        'bonus_min_deposit' => ['decimal', 'Smallest deposit that earns the welcome bonus.'],
        'bonus_max_bet'     => ['decimal', 'Largest bet allowed while bonus money is in play. 0 means no limit.'],
        'bonus_days'        => ['integer', 'Days the bonus stays alive before it expires. 0 means it never expires.'],
    ];

    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            foreach (self::COLUMNS as $name => [$type, $comment]) {
                if (Schema::hasColumn('settings', $name)) {
                    continue;
                }
                if ($type === 'integer') {
                    $table->unsignedInteger($name)->default(0)->comment($comment);
                } else {
                    $table->decimal($name, 20, 2)->default(0)->comment($comment);
                }
            }
        });

        Schema::table('wallets', function (Blueprint $table) {
            if (!Schema::hasColumn('wallets', 'bonus_expires_at')) {
                $table->timestamp('bonus_expires_at')->nullable()
                    ->comment('When the welcome bonus stops being usable.');
            }
        });

        // sensible starting values: the published welcome bonus terms
        \DB::table('settings')->update([
            'bonus_max'         => 50,
            'bonus_min_deposit' => 10,
            'bonus_max_bet'     => 2,
            'bonus_days'        => 30,
        ]);
    }

    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            foreach (array_keys(self::COLUMNS) as $name) {
                if (Schema::hasColumn('settings', $name)) {
                    $table->dropColumn($name);
                }
            }
        });

        Schema::table('wallets', function (Blueprint $table) {
            if (Schema::hasColumn('wallets', 'bonus_expires_at')) {
                $table->dropColumn('bonus_expires_at');
            }
        });
    }
};
