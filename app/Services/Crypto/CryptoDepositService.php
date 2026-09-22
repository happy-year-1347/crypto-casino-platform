<?php

namespace App\Services\Crypto;

use App\Models\Deposit;
use App\Models\Setting;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use App\Services\Wallet\DepositFinalizer;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use RuntimeException;

/**
 * Crypto deposits through NOWPayments.
 *
 * Flow: player picks a coin + fiat amount -> createPayment() stores a pending
 * Transaction + Deposit and returns the pay address -> NOWPayments calls the IPN
 * (or the player presses "check status") -> applyStatus() credits the wallet
 * once the payment is finished.
 */
class CryptoDepositService
{
    /** NOWPayments statuses that mean "money arrived, credit it". */
    public const PAID_STATUSES = ['finished', 'confirmed', 'sending'];

    /** Statuses that close the payment without money. */
    public const FAILED_STATUSES = ['failed', 'expired', 'refunded'];

    public function __construct(
        protected NowPaymentsClient $client,
        protected DepositFinalizer $finalizer,
    ) {}

    /**
     * The fiat currency prices are quoted in. NOWPayments accepts usd, eur, brl, ...
     */
    public function priceCurrency(): string
    {
        $setting = Setting::first();
        $code    = strtolower((string) ($setting->currency_code ?? 'usd'));

        return $code !== '' ? $code : 'usd';
    }

    /**
     * Coins the player can choose from: the admin list intersected with what the
     * NOWPayments account really supports.
     */
    public function availableCurrencies(): array
    {
        if (!$this->client->isEnabled()) {
            return [];
        }

        $enabled  = $this->client->enabledCurrencies();
        $merchant = $this->client->merchantCoins();

        $list = [];
        foreach ($enabled as $code => $meta) {
            if (in_array($code, $merchant, true)) {
                $list[] = ['code' => $code, 'label' => $meta['label'], 'network' => $meta['network'], 'ticker' => $this->client->tickerFor($code)];
            }
        }

        // if the merchant list could not be matched at all, show the admin list rather than nothing
        if (empty($list)) {
            foreach ($enabled as $code => $meta) {
                $list[] = ['code' => $code, 'label' => $meta['label'], 'network' => $meta['network'], 'ticker' => $this->client->tickerFor($code)];
            }
        }

        return $list;
    }

    /**
     * Live quote for the deposit form.
     */
    public function estimate(float $amount, string $currency): array
    {
        $this->assertEnabled();
        $this->assertCurrency($currency);

        $fiat     = $this->priceCurrency();
        $estimate = $this->client->estimate($amount, $fiat, $currency);

        $min = null;
        try {
            $minData = $this->client->minAmount($currency, $fiat);
            $min = [
                'crypto' => $minData['min_amount'] ?? null,
                'fiat'   => $minData['fiat_equivalent'] ?? null,
            ];
        } catch (\Throwable $e) {
            // the quote is still useful without the minimum
        }

        return [
            'amount'           => $amount,
            'price_currency'   => strtoupper($fiat),
            'pay_currency'     => strtoupper($currency),
            'estimated_amount' => $estimate['estimated_amount'] ?? null,
            'min'              => $min,
        ];
    }

    /**
     * Creates the NOWPayments payment and the pending records.
     */
    public function createPayment(User $user, float $amount, string $currency): array
    {
        $this->assertEnabled();
        $this->assertCurrency($currency);

        $setting = Setting::first();
        if ($amount < floatval($setting->min_deposit)) {
            throw new RuntimeException(__('Minimum deposit is :amount', ['amount' => $setting->min_deposit]));
        }
        if (floatval($setting->max_deposit) > 0 && $amount > floatval($setting->max_deposit)) {
            throw new RuntimeException(__('Maximum deposit is :amount', ['amount' => $setting->max_deposit]));
        }

        $wallet = Wallet::where('user_id', $user->id)->where('active', 1)->first()
            ?? Wallet::where('user_id', $user->id)->first();
        if (empty($wallet)) {
            throw new RuntimeException(__('Wallet not found'));
        }

        $fiat    = $this->priceCurrency();
        $orderId = 'dep_' . $user->id . '_' . now()->format('YmdHis') . '_' . substr(md5(uniqid('', true)), 0, 6);

        $payment = $this->client->createPayment([
            'price_amount'      => round($amount, 2),
            'price_currency'    => $fiat,
            'pay_currency'      => strtolower($currency),
            'ipn_callback_url'  => route('crypto.webhook'),
            'order_id'          => $orderId,
            'order_description' => 'Deposit #' . $user->id,
            'is_fee_paid_by_user' => $this->client->feePaidByUser(),
        ]);

        if (empty($payment['payment_id']) || empty($payment['pay_address'])) {
            throw new RuntimeException(__('Crypto provider did not return a payment address.'));
        }

        $paymentId = (string) $payment['payment_id'];

        Transaction::create([
            'payment_id'     => $paymentId,
            'user_id'        => $user->id,
            'payment_method' => 'crypto',
            'price'          => round($amount, 2),
            'currency'       => strtoupper($fiat),
            'status'         => 0,
        ]);

        $deposit = Deposit::create([
            'payment_id'          => $paymentId,
            'user_id'             => $user->id,
            'amount'              => round($amount, 2),
            'type'                => 'crypto',
            'currency'            => strtolower($currency),
            'symbol'              => $wallet->symbol,
            'status'              => 0,
            'crypto_address'      => $payment['pay_address'],
            'crypto_amount'       => $payment['pay_amount'] ?? null,
            'crypto_status'       => $payment['payment_status'] ?? 'waiting',
            'crypto_network'      => $payment['network'] ?? null,
            // NOWPayments answers in UTC; convert to the app timezone before the
            // datetime cast strips the offset for storage
            'crypto_expires_at'   => !empty($payment['expiration_estimate_date'])
                ? Carbon::parse($payment['expiration_estimate_date'])->setTimezone(config('app.timezone'))
                : now()->addMinutes(20),
        ]);

        return [
            'payment_id'     => $paymentId,
            'pay_address'    => $payment['pay_address'],
            'pay_amount'     => $payment['pay_amount'] ?? null,
            'pay_currency'   => strtoupper($payment['pay_currency'] ?? $currency),
            'network'        => $payment['network'] ?? ($this->client->enabledCurrencies()[strtolower($currency)]['network'] ?? null),
            /// what to actually print: the coin people know and the chain by the
            /// name people know it by, rather than the provider's own codes
            'pay_ticker'     => $this->client->tickerFor($currency),
            'network_label'  => $this->client->networkLabel($currency, $payment['network'] ?? null),
            'price_amount'   => $payment['price_amount'] ?? $amount,
            'price_currency' => strtoupper($payment['price_currency'] ?? $fiat),
            'payin_extra_id' => $payment['payin_extra_id'] ?? null,
            'expires_at'     => optional($deposit->crypto_expires_at)->toIso8601String(),
            'status'         => $deposit->crypto_status,
        ];
    }

    /**
     * Player pressed "check status": ask NOWPayments and apply the answer.
     */
    public function refreshStatus(User $user, string $paymentId): array
    {
        $this->assertEnabled();

        $deposit = Deposit::where('payment_id', $paymentId)->where('user_id', $user->id)->first();
        if (empty($deposit)) {
            throw new RuntimeException(__('Payment not found'));
        }

        if ((int) $deposit->status === 1) {
            return ['status' => 'finished', 'credited' => true];
        }

        $payment = $this->client->getPayment($paymentId);
        $status  = strtolower((string) ($payment['payment_status'] ?? 'waiting'));

        $this->applyStatus($deposit, $status, $payment);

        return [
            'status'        => $status,
            'credited'      => (int) $deposit->fresh()->status === 1,
            'actually_paid' => $payment['actually_paid'] ?? null,
        ];
    }

    /**
     * IPN entry point. Returns false when the signature does not match.
     */
    public function handleWebhook(array $payload, ?string $signature): bool
    {
        if (!$this->client->isEnabled()) {
            Log::warning('Crypto IPN received while the cashier is disabled');
            return false;
        }

        if (!$this->client->verifyIpnSignature($payload, $signature)) {
            Log::warning('Crypto IPN rejected: bad signature', ['payment_id' => $payload['payment_id'] ?? null]);
            return false;
        }

        $paymentId = (string) ($payload['payment_id'] ?? '');
        $status    = strtolower((string) ($payload['payment_status'] ?? ''));

        $deposit = Deposit::where('payment_id', $paymentId)->first();
        if (empty($deposit) || $status === '') {
            Log::warning('Crypto IPN for unknown payment', ['payment_id' => $paymentId]);
            return false;
        }

        $this->applyStatus($deposit, $status, $payload);

        return true;
    }

    /**
     * Single place where a NOWPayments status is turned into wallet/deposit changes.
     */
    public function applyStatus(Deposit $deposit, string $status, array $data = []): void
    {
        $deposit->update([
            'crypto_status'       => $status,
            'crypto_actually_paid' => isset($data['actually_paid']) ? $data['actually_paid'] : $deposit->crypto_actually_paid,
        ]);

        if ((int) $deposit->status !== 0) {
            return; // already credited or rejected
        }

        if (in_array($status, self::PAID_STATUSES, true)) {
            if ($this->finalizer->finalize($deposit->payment_id)) {
                Log::info('Crypto deposit credited', ['payment_id' => $deposit->payment_id, 'user_id' => $deposit->user_id]);
            }
            return;
        }

        if ($status === 'partially_paid') {
            // player sent less than the quote; leave it pending for the admin to approve
            // manually from the Deposits page after checking the received amount
            Log::warning('Crypto deposit partially paid', [
                'payment_id'    => $deposit->payment_id,
                'actually_paid' => $data['actually_paid'] ?? null,
            ]);
            return;
        }

        if (in_array($status, self::FAILED_STATUSES, true)) {
            $deposit->update(['status' => 2]);
            Transaction::where('payment_id', $deposit->payment_id)->where('status', 0)->update(['status' => 2]);
        }
    }

    protected function assertEnabled(): void
    {
        if (!$this->client->isEnabled()) {
            throw new RuntimeException(__('Crypto payments are not enabled'));
        }
    }

    protected function assertCurrency(string $currency): void
    {
        if (!$this->client->isCurrencyEnabled($currency)) {
            throw new RuntimeException(__('This cryptocurrency is not available'));
        }
    }
}
