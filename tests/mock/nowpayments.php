<?php
/**
 * Minimal NOWPayments API mock for local testing of the crypto cashier.
 *
 *   php -S 127.0.0.1:8099 tests/mock/nowpayments.php
 *   NOWPAYMENTS_API_URL=http://127.0.0.1:8099/v1/   (in .env)
 *
 * Payments are kept in a temp JSON file so status can be flipped with:
 *   curl -X POST http://127.0.0.1:8099/mock/set-status -d 'payment_id=...&status=finished'
 */

$store = sys_get_temp_dir() . '/nowpayments-mock.json';
$db    = file_exists($store) ? json_decode(file_get_contents($store), true) : ['payments' => [], 'payouts' => []];
$save  = function () use (&$db, $store) { file_put_contents($store, json_encode($db, JSON_PRETTY_PRINT)); };

$path   = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];
$body   = json_decode(file_get_contents('php://input'), true) ?: $_POST;
$apiKey = $_SERVER['HTTP_X_API_KEY'] ?? '';

header('Content-Type: application/json');

$json = function (array $data, int $code = 200) {
    http_response_code($code);
    echo json_encode($data);
    exit;
};

// test helper, not part of the real API
if ($path === '/mock/set-status') {
    $db['payments'][$body['payment_id']]['payment_status'] = $body['status'];
    if (isset($body['actually_paid'])) $db['payments'][$body['payment_id']]['actually_paid'] = (float) $body['actually_paid'];
    $save();
    $json(['ok' => true]);
}
if ($path === '/mock/reset') {
    $db = ['payments' => [], 'payouts' => []];
    $save();
    $json(['ok' => true]);
}

if ($path === '/v1/auth' && $method === 'POST') {
    if (($body['email'] ?? '') === 'merchant@example.com' && ($body['password'] ?? '') === 'secret') {
        $json(['token' => 'mock-jwt-token']);
    }
    $json(['message' => 'Invalid credentials'], 401);
}

if ($apiKey !== 'MOCK_API_KEY') {
    $json(['message' => 'Invalid api key'], 403);
}

$rates = ['btc' => 0.0000165, 'ltc' => 0.0135, 'usdttrc20' => 1.0, 'usdterc20' => 1.0, 'trx' => 8.2, 'doge' => 9.1, 'eth' => 0.00042];
$fiatMultiplier = ['usd' => 1.0, 'brl' => 0.19, 'eur' => 1.08];

if ($path === '/v1/status') {
    $json(['message' => 'OK']);
}

if ($path === '/v1/merchant/coins') {
    $json(['selectedCurrencies' => ['btc', 'ltc', 'usdttrc20', 'trx', 'doge']]);
}

if ($path === '/v1/estimate') {
    $from = strtolower($_GET['currency_from']); $to = strtolower($_GET['currency_to']);
    $amount = (float) $_GET['amount'];
    if (isset($rates[$to])) {
        $usd = $amount * ($fiatMultiplier[$from] ?? 1);
        $json(['currency_from' => $from, 'amount_from' => $amount, 'currency_to' => $to, 'estimated_amount' => round($usd * $rates[$to], 8)]);
    }
    if (isset($rates[$from])) {
        $usd = $amount / $rates[$from];
        $json(['currency_from' => $from, 'amount_from' => $amount, 'currency_to' => $to, 'estimated_amount' => round($usd / ($fiatMultiplier[$to] ?? 1), 2)]);
    }
    $json(['message' => 'Currency not supported'], 400);
}

if ($path === '/v1/min-amount') {
    $from = strtolower($_GET['currency_from']); $fiat = strtolower($_GET['fiat_equivalent'] ?? 'usd');
    $minCrypto = ($rates[$from] ?? 1) * 5; // ~5 USD
    $json(['currency_from' => $from, 'currency_to' => $_GET['currency_to'] ?? 'usd', 'min_amount' => $minCrypto, 'fiat_equivalent' => round(5 / ($fiatMultiplier[$fiat] ?? 1), 2)]);
}

if ($path === '/v1/payment' && $method === 'POST') {
    $id  = (string) random_int(4000000000, 4999999999);
    $pay = strtolower($body['pay_currency']);
    $usd = (float) $body['price_amount'] * ($fiatMultiplier[strtolower($body['price_currency'])] ?? 1);
    $payment = [
        'payment_id'      => $id,
        'payment_status'  => 'waiting',
        'pay_address'     => 'mock_' . $pay . '_' . substr(md5($id), 0, 26),
        'price_amount'    => (float) $body['price_amount'],
        'price_currency'  => $body['price_currency'],
        'pay_amount'      => round($usd * ($rates[$pay] ?? 1), 8),
        'pay_currency'    => $pay,
        'order_id'        => $body['order_id'] ?? null,
        'network'         => strtoupper(preg_replace('/(trc20|erc20)$/', '', $pay)),
        'ipn_callback_url'=> $body['ipn_callback_url'] ?? null,
        'created_at'      => date('c'),
        'expiration_estimate_date' => date('c', time() + 1200),
        'actually_paid'   => 0,
    ];
    $db['payments'][$id] = $payment;
    $save();
    $json($payment, 201);
}

if (preg_match('#^/v1/payment/(\d+)$#', $path, $m)) {
    if (isset($db['payments'][$m[1]])) $json($db['payments'][$m[1]]);
    $json(['message' => 'Payment not found'], 404);
}

// payouts need the bearer token from /auth
$bearer = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
if (str_starts_with($path, '/v1/payout')) {
    if ($bearer !== 'Bearer mock-jwt-token') $json(['message' => 'Unauthorized'], 401);

    if ($path === '/v1/payout' && $method === 'POST') {
        $batchId = (string) random_int(100000, 999999);
        $items = [];
        foreach ($body['withdrawals'] as $w) {
            $items[] = [
                'id'                  => (string) random_int(1000000, 9999999),
                'address'             => $w['address'],
                'currency'            => $w['currency'],
                'amount'              => (string) $w['amount'],
                'batch_withdrawal_id' => $batchId,
                'status'              => 'CREATING',
                'hash'                => null,
            ];
        }
        $db['payouts'][$batchId] = ['id' => $batchId, 'withdrawals' => $items];
        $save();
        $json($db['payouts'][$batchId]);
    }

    if (preg_match('#^/v1/payout/(\d+)/verify$#', $path, $m) && $method === 'POST') {
        if (($body['verification_code'] ?? '') !== '123456') $json(['message' => 'Wrong 2FA code'], 400);
        foreach ($db['payouts'][$m[1]]['withdrawals'] as &$w) { $w['status'] = 'PROCESSING'; }
        $save();
        $json(['ok' => true]);
    }

    if (preg_match('#^/v1/payout/(\d+)$#', $path, $m)) {
        if (isset($db['payouts'][$m[1]])) $json($db['payouts'][$m[1]]);
        $json(['message' => 'Batch not found'], 404);
    }
}

$json(['message' => 'Not found: ' . $path], 404);
