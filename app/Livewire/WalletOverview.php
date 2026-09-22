<?php

namespace App\Livewire;

use App\Models\Deposit;
use App\Models\Withdrawal;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\Concerns\InteractsWithPageFilters;

class WalletOverview extends BaseWidget
{
    protected static ?int $sort = -2;
    use InteractsWithPageFilters;

    /**
     * @return array|Stat[]
     */
    protected function getStats(): array
    {
        $startDate = $this->filters['startDate'] ?? null;
        $endDate = $this->filters['endDate'] ?? null;

        $setting = \Helper::getSetting();
        $dataAtual = Carbon::now();
        $depositQuery = Deposit::query();
        $withdrawalQuery = Withdrawal::query();

        if(empty($startDate) && empty($endDate)) {
            $depositQuery->whereMonth('created_at', Carbon::now()->month);
        }else{
            $depositQuery->whereBetween('created_at', [$startDate, $endDate]);
        }

        // Executa a consulta para obter a soma dos depósitos para o mês atual
        $sumDepositMonth = $depositQuery
            ->where('status', 1)
            ->sum('amount');

        $withdrawalQuery->where('status', 1);

        if(empty($startDate) && empty($endDate)) {
            $withdrawalQuery->whereMonth('created_at', Carbon::now()->month);
        }else{
            $withdrawalQuery->whereBetween('created_at', [$startDate, $endDate]);
        }

        $sumWithdrawalMonth = $withdrawalQuery->sum('amount');

        /// This used to be the revshare percentage applied to the month's
        /// deposits, which is not a real figure: nobody is owed a share of a
        /// deposit. Affiliates earn on the losses of players they referred, and
        /// only on real money, never on bonus money. Showing 20% of every
        /// deposit made it look as though an affiliate had already been paid
        /// 2.80 on a 14.00 deposit by a player who had no affiliate at all.
        $commissionAwarded = (float) \App\Models\AffiliateHistory::sum('commission_paid');
        $commissionHeld    = (float) \App\Models\Wallet::sum('refer_rewards');

        /// these three are the chosen period, not all time, and they used to say
        /// "Total". With no dates picked that period is the current month.
        $period = (empty($startDate) && empty($endDate))
            ? 'this month, ' . $dataAtual->format('F')
            : 'between the dates picked above';

        return [
            Stat::make('Deposits in', \Helper::amountFormatDecimal($sumDepositMonth))
                ->description($period)
                ->descriptionIcon('heroicon-o-arrow-down-tray')
                ->icon('heroicon-o-banknotes')
                ->color('success'),
            Stat::make('Paid out', \Helper::amountFormatDecimal($sumWithdrawalMonth))
                ->description($period)
                ->descriptionIcon('heroicon-o-arrow-up-tray')
                ->icon('heroicon-o-currency-dollar')
                ->color('danger'),
            /// this is what affiliates are owed on those deposits, not money the
            /// owner keeps, so it no longer calls itself platform earnings
            Stat::make('Affiliate commission', \Helper::amountFormatDecimal($commissionAwarded))
                ->description($commissionAwarded > 0
                    ? \Helper::amountFormatDecimal($commissionHeld) . ' still in affiliate balances'
                    : 'earned on referred players\' losses, nothing yet')
                ->descriptionIcon('heroicon-o-users')
                ->icon('heroicon-o-chart-bar')
                ->color($commissionAwarded > 0 ? 'warning' : 'gray'),
        ];
    }

    /**
     * @return bool
     */
    public static function canView(): bool
    {
        return auth()->user()->hasRole('admin');
    }
}
