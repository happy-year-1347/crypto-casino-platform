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
        Schema::table('gateways', function (Blueprint $table) {
            // Crypto payment gateway fields
            $table->string('crypto_api_key')->nullable()->after('stripe_webhook_key');
            $table->string('crypto_api_secret')->nullable();
            $table->string('crypto_webhook_secret')->nullable();
            $table->tinyInteger('crypto_is_enabled')->default(0);
            $table->json('crypto_currencies')->nullable(); // BTC, ETH, USDT, etc.
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('gateways', function (Blueprint $table) {
            $table->dropColumn([
                'crypto_api_key',
                'crypto_api_secret',
                'crypto_webhook_secret',
                'crypto_is_enabled',
                'crypto_currencies'
            ]);
        });
    }
};
