<?php

namespace App\Filament\Widgets;

use App\Models\Deposit;
use App\Models\Order;
use App\Models\User;
use App\Models\Wallet;
use App\Models\Withdrawal;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

/**
 * The first thing the owner sees when he signs in, so it answers the question he
 * actually has: how much money came in, how much went out, and what is still
 * sitting in player balances.
 *
 * The little charts are the last seven days of real figures. They used to be
 * hard-coded arrays that looked like data but were not.
 */
class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 2;

    protected static ?string $pollingInterval = '15s';

    protected static bool $isLazy = true;

    /**
     * @return array|Stat[]
     */
    protected function getStats(): array
    {
        $today       = Carbon::today();
        $sevenDaysAgo = Carbon::today()->subDays(6);

        /// only deposits the site actually credited count as money in
        $depositsToday = (float) Deposit::where('status', 1)->whereDate('updated_at', $today)->sum('amount');
        $depositsWeek  = (float) Deposit::where('status', 1)->where('updated_at', '>=', $sevenDaysAgo)->sum('amount');
        $depositsAll   = (float) Deposit::where('status', 1)->sum('amount');

        /// and only withdrawals that were actually paid count as money out
        $paidOutAll   = (float) Withdrawal::where('status', 1)->sum('amount');
        $awaitingPay  = (float) Withdrawal::where('status', 0)->sum('amount');
        $awaitingCount = Withdrawal::where('status', 0)->count();

        $heldForPlayers = (float) Wallet::sum(DB::raw('balance + balance_bonus + balance_withdrawal'));

        $staked = (float) Order::whereIn('type', ['bet', 'loss'])->sum('amount');
        $won    = (float) Order::where('type', 'win')->sum('amount');

        return [
            Stat::make('Deposits, all time', \Helper::amountFormatDecimal($depositsAll))
                ->description($depositsToday > 0
                    ? \Helper::amountFormatDecimal($depositsToday) . ' today'
                    : 'nothing yet today')
                ->descriptionIcon('heroicon-o-arrow-down-tray')
                ->icon('heroicon-o-banknotes')
                ->color('success')
                ->chart($this->dailyTotals(Deposit::class, 'amount', 1)),

            Stat::make('Last 7 days', \Helper::amountFormatDecimal($depositsWeek))
                ->description('deposits since ' . $sevenDaysAgo->format('j M'))
                ->descriptionIcon('heroicon-o-calendar-days')
                ->icon('heroicon-o-chart-bar')
                ->color('success'),

            Stat::make('Paid out to players', \Helper::amountFormatDecimal($paidOutAll))
                ->description($awaitingCount > 0
                    ? $awaitingCount . ' waiting for you, ' . \Helper::amountFormatDecimal($awaitingPay)
                    : 'nothing waiting')
                ->descriptionIcon('heroicon-o-arrow-up-tray')
                ->icon('heroicon-o-paper-airplane')
                ->color($awaitingCount > 0 ? 'warning' : 'gray')
                ->chart($this->dailyTotals(Withdrawal::class, 'amount', 1)),

            Stat::make('Held in player balances', \Helper::amountFormatDecimal($heldForPlayers))
                ->description('money players could still play or withdraw')
                ->descriptionIcon('heroicon-o-wallet')
                ->icon('heroicon-o-wallet')
                ->color('info'),

            Stat::make('Players', User::where('role_id', '!=', 0)->count())
                ->description(User::where('role_id', '!=', 0)->whereDate('created_at', $today)->count() . ' signed up today')
                ->descriptionIcon('heroicon-o-user-plus')
                ->icon('heroicon-o-users')
                ->color('info'),

            Stat::make('Staked by players', \Helper::amountFormatDecimal($staked))
                ->description(\Helper::amountFormatDecimal($won) . ' won back')
                ->descriptionIcon('heroicon-o-trophy')
                ->icon('heroicon-o-chart-pie')
                ->color($staked >= $won ? 'success' : 'danger'),
        ];
    }

    /**
     * The last seven days of a table's daily totals, oldest first, so the little
     * chart under a figure is that figure's own history rather than decoration.
     *
     * @return array<int, float>
     */
    protected function dailyTotals(string $model, string $column, int $status): array
    {
        $from = Carbon::today()->subDays(6);

        $rows = $model::where('status', $status)
            ->where('updated_at', '>=', $from)
            ->selectRaw('DATE(updated_at) as d, SUM(' . $column . ') as total')
            ->groupBy('d')
            ->pluck('total', 'd')
            ->all();

        $series = [];
        for ($i = 0; $i < 7; $i++) {
            $day = $from->copy()->addDays($i)->toDateString();
            $series[] = (float) ($rows[$day] ?? 0);
        }

        /// a flat line of zeros renders as nothing at all, so give it a floor
        return array_sum($series) > 0 ? $series : [0, 0, 0, 0, 0, 0, 0];
    }

    /**
     * @return bool
     */
    public static function canView(): bool
    {
        return auth()->user()->hasRole('admin');
    }
}
