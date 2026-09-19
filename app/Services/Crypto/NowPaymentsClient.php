<?php

namespace App\Services\Crypto;

use App\Models\Gateway;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

/**
 * Thin client for the NOWPayments REST API (https://documenter.getpostman.com/view/7907941/S1a32n38).
 *
 * Credentials come from the `gateways` row edited on the admin "Crypto Payment" page.
 * Every public method throws RuntimeException with a readable message on failure so
 * callers only need one catch.
 */
class NowPaymentsClient
{
    public const LIVE_URL    = 'https://api.nowpayments.io/v1/';
    public const SANDBOX_URL = 'https://api-sandbox.nowpayments.io/v1/';

    /**
     * Coins offered to players by default. Keys are NOWPayments currency codes,
     * values are what the player sees. The admin can narrow this list.
     */
    public const DEFAULT_CURRENCIES = [
        'btc'       => ['label' => 'Bitcoin (BTC)',            'network' => 'BTC'],
        'ltc'       => ['label' => 'Litecoin (LTC)',           'network' => 'LTC'],
        'usdttrc20' => ['label' => 'Tether (USDT) - TRC20',    'network' => 'TRON'],
        'usdterc20' => ['label' => 'Tether (USDT) - ERC20',    'network' => 'Ethereum'],
        'trx'       => ['label' => 'TRON (TRX)',               'network' => 'TRON'],
        'doge'      => ['label' => 'Dogecoin (DOGE)',          'network' => 'DOGE'],
        'eth'       => ['label' => 'Ethereum (ETH)',           'network' => 'Ethereum'],
        'usdc'      => ['label' => 'USD Coin (USDC) - ERC20',  'network' => 'Ethereum'],
        'bnbbsc'    => ['label' => 'BNB - BSC',                'network' => 'BSC'],
    ];

    protected ?Gateway $gateway;

    public function __construct(?Gateway $gateway = null)
    {
        // the container hands over a blank model when nothing is bound; only a
        // persisted row counts as an explicit gateway
        $this->gateway = ($gateway && $gateway->exists) ? $gateway : Gateway::first();
    }

    /* ------------------------------------------------------------------ */
    /*  configuration                                                      */
    /* ------------------------------------------------------------------ */

    public function isEnabled(): bool
    {
        return !empty($this->gateway)
            && (int) $this->gateway->crypto_is_enabled === 1
            && !empty($this->gateway->crypto_api_key);
    }

    public function isSandbox(): bool
    {
        return !empty($this->gateway) && (int) $this->gateway->crypto_sandbox === 1;
    }

    public function baseUrl(): string
    {
        // NOWPAYMENTS_API_URL in .env overrides both (used for local testing against a mock)
        $override = (string) config('services.nowpayments.url', '');
        if ($override !== '') {
            return rtrim($override, '/') . '/';
        }

        return $this->isSandbox() ? self::SANDBOX_URL : self::LIVE_URL;
    }

    public function apiKey(): string
    {
        return (string) ($this->gateway->crypto_api_key ?? '');
    }

    public function ipnSecret(): string
    {
        return (string) ($this->gateway->crypto_webhook_secret ?? '');
    }

    public function feePaidByUser(): bool
    {
        return !empty($this->gateway) && (int) $this->gateway->crypto_fee_paid_by_user === 1;
    }

    /**
     * Coins the admin allows, as [code => ['label' => .., 'network' => ..]].
     */
    public function enabledCurrencies(): array
    {
        $configured = $this->gateway->crypto_currencies ?? null;
        if (is_string($configured)) {
            $configured = json_decode($configured, true);
        }

        if (empty($configured) || !is_array($configured)) {
            return self::DEFAULT_CURRENCIES;
        }

        $list = [];
        foreach ($configured as $code) {
            $code = strtolower(trim((string) $code));
            if ($code === '') continue;
            $list[$code] = self::DEFAULT_CURRENCIES[$code] ?? ['label' => strtoupper($code), 'network' => strtoupper($code)];
        }

        return $list ?: self::DEFAULT_CURRENCIES;
    }

    public function isCurrencyEnabled(string $code): bool
    {
        return array_key_exists(strtolower($code), $this->enabledCurrencies());
    }

    /* ------------------------------------------------------------------ */
    /*  public API                                                         */
    /* ------------------------------------------------------------------ */

    /** API availability check. */
    public function status(): bool
    {
        try {
            return $this->request()->get($this->baseUrl() . 'status')->json('message') === 'OK';
        } catch (\Throwable $e) {
            return false;
        }
    }

    /**
     * Coins actually switched on in the merchant's NOWPayments account.
     * Cached for 10 minutes; falls back to the admin list when the call fails.
     */
    public function merchantCoins(): array
    {
        $key = 'nowpayments:merchant-coins:' . md5($this->apiKey() . $this->baseUrl());

        return Cache::remember($key, 600, function () {
            try {
                $response = $this->request()->get($this->baseUrl() . 'merchant/coins');
                $coins    = $response->json('selectedCurrencies');
                if (is_array($coins) && !empty($coins)) {
                    return array_map('strtolower', $coins);
                }
            } catch (\Throwable $e) {
                Log::warning('NOWPayments merchant/coins failed: ' . $e->getMessage());
            }

            return array_keys($this->enabledCurrencies());
        });
    }

    /**
     * Live conversion: how much crypto the player has to send for a fiat amount.
     */
    public function estimate(float $amount, string $from, string $to): array
    {
        $response = $this->request()->get($this->baseUrl() . 'estimate', [
            'amount'        => $amount,
            'currency_from' => strtolower($from),
            'currency_to'   => strtolower($to),
        ]);

        return $this->decode($response, 'estimate');
    }

    /**
     * Smallest payment NOWPayments accepts for a coin, with its fiat equivalent.
     */
    public function minAmount(string $currency, string $fiat): array
    {
        $response = $this->request()->get($this->baseUrl() . 'min-amount', [
            'currency_from'   => strtolower($currency),
            'currency_to'     => strtolower($fiat),
            'fiat_equivalent' => strtolower($fiat),
        ]);

        return $this->decode($response, 'min-amount');
    }

    /**
     * Creates a payment and returns the pay address + amount to send.
     */
    public function createPayment(array $payload): array
    {
        $response = $this->request()->post($this->baseUrl() . 'payment', $payload);

        return $this->decode($response, 'payment');
    }

    public function getPayment(string $paymentId): array
    {
        $response = $this->request()->get($this->baseUrl() . 'payment/' . $paymentId);

        return $this->decode($response, 'payment/' . $paymentId);
    }

    /* ------------------------------------------------------------------ */
    /*  payouts (withdrawals)                                              */
    /* ------------------------------------------------------------------ */

    /**
     * Payout endpoints need a JWT obtained with the account email + password.
     */
    public function payoutToken(): string
    {
        $email    = (string) ($this->gateway->crypto_payout_email ?? '');
        $password = $this->payoutPassword();

        if ($email === '' || $password === '') {
            throw new RuntimeException('NOWPayments payout e-mail and password are not configured.');
        }

        $response = Http::acceptJson()->timeout(30)->post($this->baseUrl() . 'auth', [
            'email'    => $email,
            'password' => $password,
        ]);

        $data = $this->decode($response, 'auth');
        if (empty($data['token'])) {
            throw new RuntimeException('NOWPayments did not return a payout token.');
        }

        return $data['token'];
    }

    /**
     * Creates a payout batch. Each withdrawal: ['address' => .., 'currency' => .., 'amount' => .., 'extra_id' => ..?]
     */
    public function createPayout(array $withdrawals, string $ipnCallbackUrl): array
    {
        $token = $this->payoutToken();

        $response = $this->request()
            ->withToken($token)
            ->post($this->baseUrl() . 'payout', [
                'ipn_callback_url' => $ipnCallbackUrl,
                'withdrawals'      => $withdrawals,
            ]);

        return $this->decode($response, 'payout');
    }

    /**
     * Confirms a payout batch with the 2FA code from the NOWPayments authenticator app.
     */
    public function verifyPayout(string $batchId, string $code): array
    {
        $token = $this->payoutToken();

        $response = $this->request()
            ->withToken($token)
            ->post($this->baseUrl() . 'payout/' . $batchId . '/verify', [
                'verification_code' => $code,
            ]);

        return $this->decode($response, 'payout/verify');
    }

    public function getPayout(string $batchId): array
    {
        $token = $this->payoutToken();

        $response = $this->request()
            ->withToken($token)
            ->get($this->baseUrl() . 'payout/' . $batchId);

        return $this->decode($response, 'payout/' . $batchId);
    }

    /* ------------------------------------------------------------------ */
    /*  IPN signature                                                      */
    /* ------------------------------------------------------------------ */

    /**
     * NOWPayments signs every IPN with HMAC-SHA512 over the JSON body with keys
     * sorted recursively, using the IPN secret from the dashboard.
     */
    public function verifyIpnSignature(array $payload, ?string $signature): bool
    {
        $secret = $this->ipnSecret();
        if ($secret === '' || empty($signature)) {
            return false;
        }

        $sorted    = $this->ksortRecursive($payload);
        $signature = strtolower(trim($signature));
        $secret    = trim($secret);

        // NOWPayments' reference implementation uses JSON_UNESCAPED_SLASHES; the
        // other two encodings cover payloads with unicode or escaped slashes.
        foreach ([JSON_UNESCAPED_SLASHES, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE, 0] as $flags) {
            $hmac = hash_hmac('sha512', json_encode($sorted, $flags), $secret);
            if (hash_equals($hmac, $signature)) {
                return true;
            }
        }

        return false;
    }

    public function ksortRecursive(array $data): array
    {
        ksort($data);
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $data[$key] = $this->ksortRecursive($value);
            }
        }

        return $data;
    }

    /* ------------------------------------------------------------------ */
    /*  helpers                                                            */
    /* ------------------------------------------------------------------ */

    public static function encryptSecret(?string $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        return Crypt::encryptString($value);
    }

    protected function payoutPassword(): string
    {
        $stored = (string) ($this->gateway->crypto_payout_password ?? '');
        if ($stored === '') {
            return '';
        }

        try {
            return Crypt::decryptString($stored);
        } catch (\Throwable $e) {
            // value was saved before encryption was introduced
            return $stored;
        }
    }

    protected function request(): PendingRequest
    {
        if ($this->apiKey() === '') {
            throw new RuntimeException('NOWPayments API key is not configured.');
        }

        return Http::acceptJson()
            ->timeout(30)
            ->withHeaders(['x-api-key' => $this->apiKey()]);
    }

    protected function decode(Response $response, string $endpoint): array
    {
        $data = $response->json();

        if (!$response->successful()) {
            $message = is_array($data)
                ? ($data['message'] ?? $data['error'] ?? $response->body())
                : $response->body();

            Log::warning("NOWPayments {$endpoint} failed ({$response->status()}): {$message}");
            throw new RuntimeException('Crypto provider error: ' . $message);
        }

        return is_array($data) ? $data : [];
    }
}
