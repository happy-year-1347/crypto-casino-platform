<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Columns needed by the NOWPayments crypto cashier (deposits + payouts).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('gateways', function (Blueprint $table) {
            if (!Schema::hasColumn('gateways', 'crypto_sandbox')) {
                $table->tinyInteger('crypto_sandbox')->default(0)->after('crypto_is_enabled');
            }
            if (!Schema::hasColumn('gateways', 'crypto_payout_email')) {
                $table->string('crypto_payout_email')->nullable()->after('crypto_currencies');
            }
            if (!Schema::hasColumn('gateways', 'crypto_payout_password')) {
                $table->text('crypto_payout_password')->nullable()->after('crypto_payout_email');
            }
            if (!Schema::hasColumn('gateways', 'crypto_fee_paid_by_user')) {
                $table->tinyInteger('crypto_fee_paid_by_user')->default(0)->after('crypto_payout_password');
            }
        });

        Schema::table('deposits', function (Blueprint $table) {
            if (!Schema::hasColumn('deposits', 'crypto_status')) {
                $table->string('crypto_status', 30)->nullable()->after('crypto_amount');
            }
            if (!Schema::hasColumn('deposits', 'crypto_actually_paid')) {
                $table->decimal('crypto_actually_paid', 20, 8)->nullable()->after('crypto_status');
            }
            if (!Schema::hasColumn('deposits', 'crypto_network')) {
                $table->string('crypto_network', 30)->nullable()->after('crypto_actually_paid');
            }
            if (!Schema::hasColumn('deposits', 'crypto_expires_at')) {
                $table->timestamp('crypto_expires_at')->nullable()->after('crypto_network');
            }
        });

        Schema::table('withdrawals', function (Blueprint $table) {
            if (!Schema::hasColumn('withdrawals', 'crypto_amount')) {
                $table->decimal('crypto_amount', 20, 8)->nullable()->after('crypto_address');
            }
            if (!Schema::hasColumn('withdrawals', 'crypto_payout_id')) {
                $table->string('crypto_payout_id', 64)->nullable()->after('crypto_amount');
            }
            if (!Schema::hasColumn('withdrawals', 'crypto_batch_id')) {
                $table->string('crypto_batch_id', 64)->nullable()->after('crypto_payout_id');
            }
            if (!Schema::hasColumn('withdrawals', 'crypto_status')) {
                $table->string('crypto_status', 30)->nullable()->after('crypto_batch_id');
            }
            if (!Schema::hasColumn('withdrawals', 'crypto_tx_hash')) {
                $table->string('crypto_tx_hash', 191)->nullable()->after('crypto_status');
            }
        });
    }

    public function down(): void
    {
        Schema::table('gateways', function (Blueprint $table) {
            $table->dropColumn(['crypto_sandbox', 'crypto_payout_email', 'crypto_payout_password', 'crypto_fee_paid_by_user']);
        });
        Schema::table('deposits', function (Blueprint $table) {
            $table->dropColumn(['crypto_status', 'crypto_actually_paid', 'crypto_network', 'crypto_expires_at']);
        });
        Schema::table('withdrawals', function (Blueprint $table) {
            $table->dropColumn(['crypto_amount', 'crypto_payout_id', 'crypto_batch_id', 'crypto_status', 'crypto_tx_hash']);
        });
    }
};
