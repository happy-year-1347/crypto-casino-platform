<?php

namespace App\Services\Wallet;

use App\Helpers\Core as Helper;
use App\Models\AffiliateHistory;
use App\Models\Deposit;
use App\Models\Setting;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use App\Notifications\NewDepositNotification;
use Illuminate\Support\Facades\DB;

/**
 * Credits a confirmed deposit to the player's wallet.
 *
 * Same rules as the PIX flow (SuitpayTrait::finalizePayment): first-deposit
 * bonus + bonus rollover, deposit rollover, VIP points, affiliate CPA and the
 * admin notification. It is idempotent: a transaction that is already status 1
 * is never credited twice.
 */
class DepositFinalizer
{
    public function finalize(string $paymentId): bool
    {
        return DB::transaction(function () use ($paymentId) {
            $transaction = Transaction::where('payment_id', $paymentId)
                ->where('status', 0)
                ->lockForUpdate()
                ->first();

            if (empty($transaction)) {
                return false;
            }

            $user   = User::find($transaction->user_id);
            $wallet = Wallet::where('user_id', $transaction->user_id)->where('active', 1)->first()
                ?? Wallet::where('user_id', $transaction->user_id)->first();

            if (empty($user) || empty($wallet)) {
                return false;
            }

            $setting = Setting::first();

            /// first completed deposit pays the welcome bonus, capped and with a
            /// qualifying minimum, the way the published terms describe it
            $completed = Transaction::where('user_id', $transaction->user_id)->where('status', 1)->count();
            if ($completed == 0) {
                $bonus = Helper::welcomeBonus($setting, $transaction->price);

                if ($bonus > 0) {
                    $wallet->increment('balance_bonus', $bonus);

                    $update = ['balance_bonus_rollover' => $bonus * $setting->rollover];

                    /// the bonus has a life span; after it, whatever is left of it goes
                    $days = intval($setting->bonus_days ?? 0);
                    if ($days > 0) {
                        $update['bonus_expires_at'] = now()->addDays($days);
                    }

                    $wallet->update($update);
                }
            }

            /// deposit rollover before the money can be withdrawn. It adds to what
            /// is already owed: the gateway code that came with the script assigned
            /// it instead, so a second small deposit wiped out the rollover still
            /// owed on a large first one.
            $depositRollover = $transaction->price * intval($setting->rollover_deposit);
            if ($depositRollover > 0) {
                $wallet->increment('balance_deposit_rollover', $depositRollover);
            }

            Helper::payBonusVip($wallet, $transaction->price);

            $wallet->increment('balance', $transaction->price);
            $transaction->update(['status' => 1]);

            $deposit = Deposit::where('payment_id', $paymentId)->where('status', 0)->first();
            if (!empty($deposit)) {
                $this->payAffiliateCpa($user, $deposit, $transaction);
                $deposit->update(['status' => 1]);
            }

            foreach (User::where('role_id', 0)->get() as $admin) {
                try {
                    $admin->notify(new NewDepositNotification($user->name, $transaction->price));
                } catch (\Throwable $e) {
                    // a broken mail setup must never block a credited deposit
                }
            }

            return true;
        });
    }

    protected function payAffiliateCpa(User $user, Deposit $deposit, Transaction $transaction): void
    {
        $affHistoryCPA = AffiliateHistory::where('user_id', $user->id)
            ->where('commission_type', 'cpa')
            ->where('status', 0)
            ->first();

        if (empty($affHistoryCPA)) {
            return;
        }

        $sponsor = User::find($user->inviter);
        if (empty($sponsor)) {
            return;
        }

        if ($affHistoryCPA->deposited_amount >= $sponsor->affiliate_baseline || $deposit->amount >= $sponsor->affiliate_baseline) {
            $walletCpa = Wallet::where('user_id', $affHistoryCPA->inviter)->first();
            if (!empty($walletCpa)) {
                $walletCpa->increment('refer_rewards', $sponsor->affiliate_cpa);
                $affHistoryCPA->update(['status' => 1, 'commission_paid' => $sponsor->affiliate_cpa]);
            }
        } else {
            $affHistoryCPA->update(['deposited_amount' => $transaction->price]);
        }
    }
}
