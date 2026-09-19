<?php
/**
 * Runs ONE withdrawal request through the real controller, in its own process.
 * Several of these are started at once with the same start time, so they hit the
 * balance check together. (`php artisan serve` is single threaded, so the races
 * cannot be reproduced through HTTP on a dev machine.)
 *
 *   php tests/mock/parallel-withdraw.php <email> <amount> <startUnixMs>
 */
require __DIR__ . '/../../vendor/autoload.php';

$app    = require __DIR__ . '/../../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::create('/', 'GET'));

use App\Http\Controllers\Api\Profile\WalletController;
use App\Models\User;
use Illuminate\Http\Request;

$email  = $argv[1];
$amount = (float) $argv[2];
$startAt = isset($argv[3]) ? (float) $argv[3] : 0;

$user = User::where('email', $email)->firstOrFail();
auth('api')->login($user);

// all processes jump at the same instant
while ($startAt > 0 && microtime(true) * 1000 < $startAt) {
    usleep(200);
}

$request = Request::create('/api/wallet/withdraw/request', 'POST', [
    'type'            => 'crypto',
    'amount'          => $amount,
    'crypto_currency' => 'doge',
    'crypto_address'  => 'DHy4P1xkzgbUZqTz4KexA1sKikQ3xR2mock',
    'accept_terms'    => true,
]);

try {
    $response = app(WalletController::class)->requestWithdrawal($request);
    echo $response->getStatusCode(), "\n";
} catch (\Throwable $e) {
    echo "500 ", $e->getMessage(), "\n";
}
