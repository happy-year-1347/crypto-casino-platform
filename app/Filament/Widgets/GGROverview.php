<?php

namespace App\Filament\Widgets;

use App\Models\GGRGamesFiver;
use App\Models\GgrGamesWorldSlot;
use App\Traits\Providers\FiversTrait;
use App\Traits\Providers\WorldSlotTrait;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class GGROverview extends BaseWidget
{
    use WorldSlotTrait;

    protected function getStats(): array
    {
        $balance = self::getWorldSlotBalance();
        $creditoGastos = GgrGamesWorldSlot::sum('balance_bet');
        $totalPartidas = GgrGamesWorldSlot::count();

        return [
            Stat::make('Fivers Credits', ($balance ?? '0'))
                ->description('Current balance in World Slot')
                ->descriptionIcon('heroicon-o-arrow-trending-up')
                ->icon('heroicon-o-wallet')
                ->color('success')
                ->chart([50, 55, 60, 65, 70, 75, 80]),
            Stat::make('Fivers Spent Credits', \Helper::amountFormatDecimal($creditoGastos))
                ->description('Credits spent by users')
                ->descriptionIcon('heroicon-o-credit-card')
                ->icon('heroicon-o-currency-dollar')
                ->color('warning')
                ->chart([20, 25, 30, 35, 40, 45, 50]),
            Stat::make('Total Fivers Matches', $totalPartidas)
                ->description('Total World Slot Matches')
                ->descriptionIcon('heroicon-o-play')
                ->icon('heroicon-o-puzzle-piece')
                ->color('info')
                ->chart([100, 150, 200, 250, 300, 350, 400]),
        ];
    }

    /**
     * @return bool
     */
    public static function canView(): bool
    {
        /// "Fivers" and "World Slot" are a third party games provider that came
        /// with the script and that this casino does not use: the twelve slots
        /// run on this server. The panel showed three of its figures, one of them
        /// blank, on a dashboard meant to answer "how much money came in". Hidden
        /// rather than deleted, so turning the provider on later only needs this
        /// line changed back.
        return false;
    }
}
