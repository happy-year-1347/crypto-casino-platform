<?php

namespace App\Console\Commands\Crypto;

use App\Models\Deposit;
use App\Services\Crypto\CryptoDepositService;
use App\Services\Crypto\NowPaymentsClient;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

/**
 * Asks the payment provider about every deposit the site has not settled yet.
 *
 * The site normally learns that a payment finished from the provider's callback.
 * A callback can be missed: the server restarts at the wrong second, the network
 * hiccups, the provider gives up retrying. Nothing else in the site would ever
 * notice, and a player who really sent money would never be credited. This asks
 * instead of waiting, so a lost callback costs nobody anything.
 *
 * It also settles abandoned attempts. Somebody who opens the cashier and never
 * pays leaves a row sitting at "waiting" for good, which keeps a count on the
 * Deposits menu that never goes away.
 *
 * Crediting goes through the same applyStatus() the callback uses, which refuses
 * to credit a deposit twice, so running this often is safe.
 */
class ReconcileDeposits extends Command
{
    protected $signature = 'crypto:reconcile
                            {--days=7 : how far back to look}
                            {--limit=100 : most rows to check in one run}';

    protected $description = 'Ask the payment provider about deposits that have not settled yet';

    public function handle(NowPaymentsClient $client, CryptoDepositService $deposits): int
    {
        if (!$client->isEnabled()) {
            $this->info('The cashier is switched off, nothing to do.');
            return self::SUCCESS;
        }

        $pending = Deposit::where('type', 'crypto')
            ->where('status', 0)
            ->where('created_at', '>=', now()->subDays((int) $this->option('days')))
            ->orderBy('id')
            ->limit((int) $this->option('limit'))
            ->get();

        if ($pending->isEmpty()) {
            $this->info('Nothing is waiting.');
            return self::SUCCESS;
        }

        $this->info($pending->count() . ' deposit(s) to ask about.');

        $credited = 0;
        $closed   = 0;
        $failed   = 0;

        foreach ($pending as $deposit) {
            try {
                $payment = $client->getPayment($deposit->payment_id);
                $status  = strtolower((string) ($payment['payment_status'] ?? ''));

                if ($status === '') {
                    continue;
                }

                $was = (int) $deposit->status;
                $deposits->applyStatus($deposit, $status, $payment);
                $now = (int) $deposit->fresh()->status;

                if ($was === 0 && $now === 1) {
                    $credited++;
                    /// this is the case the whole command exists for
                    Log::warning('Deposit credited by reconcile, its callback never arrived', [
                        'payment_id' => $deposit->payment_id,
                        'user_id'    => $deposit->user_id,
                        'amount'     => $deposit->amount,
                    ]);
                    $this->line('  credited  ' . $deposit->payment_id . '  ' . $deposit->amount);
                } elseif ($was === 0 && $now === 2) {
                    $closed++;
                    $this->line('  closed    ' . $deposit->payment_id . '  (' . $status . ')');
                } else {
                    $this->line('  still ' . $status . '  ' . $deposit->payment_id);
                }
            } catch (\Throwable $e) {
                $failed++;
                Log::warning('Could not reconcile a deposit', [
                    'payment_id' => $deposit->payment_id,
                    'error'      => $e->getMessage(),
                ]);
                $this->line('  could not check ' . $deposit->payment_id . ': ' . $e->getMessage());
            }
        }

        $this->info("credited {$credited}, closed {$closed}, could not check {$failed}");

        return self::SUCCESS;
    }
}
