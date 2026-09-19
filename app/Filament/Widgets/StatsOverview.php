<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use App\Models\User;
use App\Models\Wallet;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

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
        $sevenDaysAgo = Carbon::now()->subDays(7);

        $totalApostas = Order::whereIn('type', ['bet', 'loss'])->sum('amount');
        $totalWins = Order::where('type', 'win')->sum('amount');

        $totalWonLast7Days = $totalWins;
        $totalLoseLast7Days = $totalApostas;

        return [
            Stat::make('Total Users', User::count())
                ->description('New users')
                ->descriptionIcon('heroicon-o-user-plus')
                ->icon('heroicon-o-users')
                ->color('info')
                ->chart([7, 8, 10, 12, 15, 18, 20]),
            Stat::make('Total Wins', \Helper::amountFormatDecimal($totalWonLast7Days))
                ->description('User winnings')
                ->descriptionIcon('heroicon-o-trophy')
                ->icon('heroicon-o-sparkles')
                ->color('success')
                ->chart([10, 15, 20, 25, 30, 35, 40]),
            Stat::make('Total Losses', \Helper::amountFormatDecimal($totalLoseLast7Days))
                ->description('User losses')
                ->descriptionIcon('heroicon-o-arrow-trending-down')
                ->icon('heroicon-o-chart-pie')
                ->color('danger')
                ->chart([40, 35, 30, 28, 25, 22, 20])
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
