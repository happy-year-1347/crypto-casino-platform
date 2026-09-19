<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('withdrawals', function (Blueprint $table) {
            // store the crypto symbol (eg. btc, eth)
            $table->string('crypto_currency', 50)->nullable()->after('currency');
            // store the destination address for crypto withdrawals
            $table->text('crypto_address')->nullable()->after('crypto_currency');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('withdrawals', function (Blueprint $table) {
            $table->dropColumn(['crypto_currency', 'crypto_address']);
        });
    }
};
