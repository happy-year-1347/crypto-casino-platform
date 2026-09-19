<?php
/**
 * Local test fixture: enables the crypto cashier against the mock NOWPayments
 * server and gives limits that make testing easy.
 *
 *   php artisan tinker --execute="require 'tests/mock/seed-local.php';"
 */

use App\Models\Gateway;
use App\Models\Setting;
use App\Services\Crypto\NowPaymentsClient;
use Illuminate\Support\Facades\Cache;

$gateway = Gateway::first() ?? new Gateway();
$gateway->fill([
    'crypto_is_enabled'       => 1,
    'crypto_sandbox'          => 0,
    'crypto_api_key'          => 'MOCK_API_KEY',
    'crypto_webhook_secret'   => 'MOCK_IPN_SECRET',
    'crypto_currencies'       => ['btc', 'ltc', 'usdttrc20', 'trx', 'doge'],
    'crypto_payout_email'     => 'merchant@example.com',
    'crypto_payout_password'  => NowPaymentsClient::encryptSecret('secret'),
    'crypto_fee_paid_by_user' => 0,
])->save();

Setting::query()->update([
    'crypto_is_enabled' => 1,
    'min_deposit'       => 10,
    'max_deposit'       => 5000,
    'min_withdrawal'    => 20,
    'max_withdrawal'    => 5000,
    'withdrawal_limit'  => 5,        // per player per period
    'withdrawal_period' => 'daily',
]);

Cache::flush();

echo "crypto cashier enabled against the mock\n";
