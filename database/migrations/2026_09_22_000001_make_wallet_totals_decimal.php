<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * The running totals a wallet keeps for winnings and losses were whole-number
 * columns, so every win was rounded as it was added up: a 0.75 win was recorded
 * as 1, a 0.40 win as 0. The money itself was always moved correctly, to the
 * penny, but the figures the owner reads were not.
 *
 * At small stakes most wins are under a pound, so the error does not average
 * out in his favour or the players' in any predictable way, it simply makes the
 * number untrustworthy. Widening the columns fixes it from here on. Values
 * already stored keep whatever they were rounded to; nothing is lost.
 */
return new class extends Migration
{
    /** column => the whole-number type it used to be */
    protected array $columns = [
        'total_won'  => 'bigint(20) NOT NULL DEFAULT 0',
        'total_lose' => 'bigint(20) NOT NULL DEFAULT 0',
        'last_won'   => 'bigint(20) NOT NULL DEFAULT 0',
        'last_lose'  => 'bigint(20) NOT NULL DEFAULT 0',
    ];

    public function up(): void
    {
        foreach (array_keys($this->columns) as $column) {
            if (!Schema::hasColumn('wallets', $column)) {
                continue;
            }

            DB::statement(
                'ALTER TABLE `wallets` MODIFY `' . $column . '` DECIMAL(20,2) NOT NULL DEFAULT 0.00'
            );
        }
    }

    public function down(): void
    {
        foreach ($this->columns as $column => $type) {
            if (!Schema::hasColumn('wallets', $column)) {
                continue;
            }

            DB::statement('ALTER TABLE `wallets` MODIFY `' . $column . '` ' . $type);
        }
    }
};
