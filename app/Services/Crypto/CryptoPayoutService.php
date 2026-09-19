<?php

namespace App\Services\Crypto;

use App\Models\Setting;
use App\Models\Wallet;
use App\Models\Withdrawal;
use Illuminate\Support\Facades\Log;
use RuntimeException;

/**
 * Crypto withdrawals (NOWPayments "payouts").
 *
 * A withdrawal request already took the fiat amount out of the player's
 * balance_withdrawal. From the admin Withdrawals page the operator can:
 *   - send it through NOWPayments (needs the 2FA code from the NOWPayments app),
 *   - mark it paid manually after sending from their own wallet,
 *   - cancel it, which refunds the player.
 */
class CryptoPayoutService
{
    /** Withdrawal.status values used by the whole platform. */
    public const STATUS_PENDING  = 0;
    public const STATUS_PAID     = 1;
    public const STATUS_CANCELED = 2;

    public const FINAL_OK     = ['finished'];
    public const FINAL_FAILED = ['failed', 'rejected'];

    public function __construct(protected NowPaymentsClient $client) {}

    public function priceCurrency(): string
    {
        $setting = Setting::first();
        $code    = strtolower((string) ($setting->currency_code ?? 'usd'));

        return $code !== '' ? $code : 'usd';
    }

    /**
     * How much crypto the fiat amount is worth right now.
     */
    public function quote(Withdrawal $withdrawal): float
    {
        $estimate = $this->client->estimate(
            floatval($withdrawal->amount),
            $this->priceCurrency(),
            (string) $withdrawal->crypto_currency
        );

        $amount = floatval($estimate['estimated_amount'] ?? 0);
        if ($amount <= 0) {
            throw new RuntimeException('Could not get a conversion rate for ' . strtoupper($withdrawal->crypto_currency));
        }

        return $amount;
    }

    /**
     * Step 1: create the payout batch at NOWPayments.
     */
    public function send(Withdrawal $withdrawal): array
    {
        $this->assertCrypto($withdrawal);

        if ((int) $withdrawal->status !== self::STATUS_PENDING) {
            throw new RuntimeException('This withdrawal is not pending.');
        }
        if (!empty($withdrawal->crypto_batch_id)) {
            throw new RuntimeException('A payout was already created for this withdrawal (batch ' . $withdrawal->crypto_batch_id . ').');
        }

        $cryptoAmount = $this->quote($withdrawal);

        $result = $this->client->createPayout([[
            'address'  => $withdrawal->crypto_address,
            'currency' => strtolower($withdrawal->crypto_currency),
            'amount'   => $cryptoAmount,
        ]], route('crypto.payout.webhook'));

        $batchId = (string) ($result['id'] ?? '');
        $item    = $result['withdrawals'][0] ?? [];

        if ($batchId === '') {
            throw new RuntimeException('NOWPayments did not return a payout batch id.');
        }

        $withdrawal->update([
            'crypto_amount'    => $cryptoAmount,
            'crypto_batch_id'  => $batchId,
            'crypto_payout_id' => $item['id'] ?? null,
            'crypto_status'    => strtolower((string) ($item['status'] ?? 'creating')),
        ]);

        return ['batch_id' => $batchId, 'crypto_amount' => $cryptoAmount];
    }

    /**
     * Step 2: confirm the batch with the 2FA code. NOWPayments then starts sending.
     */
    public function verify(Withdrawal $withdrawal, string $code): void
    {
        $this->assertCrypto($withdrawal);

        if (empty($withdrawal->crypto_batch_id)) {
            throw new RuntimeException('Create the payout first.');
        }

        $this->client->verifyPayout($withdrawal->crypto_batch_id, trim($code));
        $withdrawal->update(['crypto_status' => 'processing']);
    }

    /**
     * Convenience: create + verify in one go from the admin action.
     */
    public function sendAndVerify(Withdrawal $withdrawal, string $code): array
    {
        $result = $this->send($withdrawal);
        $this->verify($withdrawal, $code);

        return $result;
    }

    /**
     * Ask NOWPayments for the batch state and apply it.
     */
    public function refresh(Withdrawal $withdrawal): string
    {
        $this->assertCrypto($withdrawal);

        if (empty($withdrawal->crypto_batch_id)) {
            return (string) $withdrawal->crypto_status;
        }

        $batch = $this->client->getPayout($withdrawal->crypto_batch_id);
        $items = $batch['withdrawals'] ?? [];

        foreach ($items as $item) {
            if ((string) ($item['id'] ?? '') === (string) $withdrawal->crypto_payout_id || count($items) === 1) {
                $this->applyStatus($withdrawal, strtolower((string) ($item['status'] ?? '')), $item);
            }
        }

        return (string) $withdrawal->fresh()->crypto_status;
    }

    /**
     * Payout IPN. Payload has batch_withdrawal_id / id / status / hash.
     */
    public function handleWebhook(array $payload, ?string $signature): bool
    {
        if (!$this->client->verifyIpnSignature($payload, $signature)) {
            Log::warning('Crypto payout IPN rejected: bad signature', ['id' => $payload['id'] ?? null]);
            return false;
        }

        $payoutId = (string) ($payload['id'] ?? '');
        $batchId  = (string) ($payload['batch_withdrawal_id'] ?? '');

        $withdrawal = Withdrawal::query()
            ->when($payoutId !== '', fn ($q) => $q->where('crypto_payout_id', $payoutId))
            ->when($payoutId === '' && $batchId !== '', fn ($q) => $q->where('crypto_batch_id', $batchId))
            ->first();

        if (empty($withdrawal)) {
            Log::warning('Crypto payout IPN for unknown withdrawal', ['id' => $payoutId, 'batch' => $batchId]);
            return false;
        }

        $this->applyStatus($withdrawal, strtolower((string) ($payload['status'] ?? '')), $payload);

        return true;
    }

    public function applyStatus(Withdrawal $withdrawal, string $status, array $data = []): void
    {
        if ($status === '') {
            return;
        }

        $update = ['crypto_status' => $status];
        if (!empty($data['hash'])) {
            $update['crypto_tx_hash'] = $data['hash'];
        }
        $withdrawal->update($update);

        if ((int) $withdrawal->status !== self::STATUS_PENDING) {
            return;
        }

        if (in_array($status, self::FINAL_OK, true)) {
            $paid = Withdrawal::whereKey($withdrawal->id)
                ->where('status', self::STATUS_PENDING)
                ->update(['status' => self::STATUS_PAID]);
            if ($paid) {
                $withdrawal->refresh();
                Log::info('Crypto withdrawal paid', ['withdrawal_id' => $withdrawal->id]);
            }
        } elseif (in_array($status, self::FINAL_FAILED, true)) {
            try {
                $this->cancel($withdrawal, 'NOWPayments reported ' . $status);
            } catch (RuntimeException $e) {
                // another callback already closed this withdrawal
                Log::info('Payout IPN ignored, withdrawal no longer pending', ['withdrawal_id' => $withdrawal->id]);
            }
        }
    }

    /**
     * Operator sent the coins from their own wallet.
     */
    public function markPaidManually(Withdrawal $withdrawal, ?string $txHash = null): void
    {
        $this->assertCrypto($withdrawal);

        // only one of "mark paid" / "cancel" / an incoming IPN may win
        $claimed = Withdrawal::whereKey($withdrawal->id)
            ->where('status', self::STATUS_PENDING)
            ->update([
                'status'         => self::STATUS_PAID,
                'crypto_status'  => 'finished',
                'crypto_tx_hash' => $txHash ?: $withdrawal->crypto_tx_hash,
            ]);

        if (!$claimed) {
            throw new RuntimeException('This withdrawal is not pending.');
        }

        $withdrawal->refresh();
    }

    /**
     * Cancels a pending withdrawal and gives the fiat amount back to the player.
     */
    public function cancel(Withdrawal $withdrawal, ?string $reason = null): void
    {
        // claim the row first: a second cancel (or a failed-payout IPN arriving at
        // the same moment) must not refund the player twice
        $claimed = Withdrawal::whereKey($withdrawal->id)
            ->where('status', self::STATUS_PENDING)
            ->update([
                'status'        => self::STATUS_CANCELED,
                'crypto_status' => $withdrawal->crypto_status ?: 'canceled',
            ]);

        if (!$claimed) {
            throw new RuntimeException('This withdrawal is not pending.');
        }

        $withdrawal->refresh();

        $wallet = Wallet::where('user_id', $withdrawal->user_id)->where('active', 1)->first()
            ?? Wallet::where('user_id', $withdrawal->user_id)->first();

        if (!empty($wallet)) {
            $wallet->increment('balance_withdrawal', floatval($withdrawal->amount));
        } else {
            Log::error('Withdrawal canceled but no wallet to refund', ['withdrawal_id' => $withdrawal->id]);
        }

        Log::info('Withdrawal canceled and refunded', ['withdrawal_id' => $withdrawal->id, 'reason' => $reason]);
    }

    protected function assertCrypto(Withdrawal $withdrawal): void
    {
        if ($withdrawal->type !== 'crypto' || empty($withdrawal->crypto_address) || empty($withdrawal->crypto_currency)) {
            throw new RuntimeException('This is not a crypto withdrawal.');
        }
    }
}
