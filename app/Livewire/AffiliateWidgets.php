<?php

namespace App\Livewire;

use App\Models\AffiliateHistory;
use App\Models\User;
use App\Models\Wallet;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AffiliateWidgets extends BaseWidget
{
    protected static ?int $navigationSort = -2;

    /**
     * @return array|Stat[]
     */
    protected function getCards(): array
    {
        $inviterId      = auth()->user()->id;
        $usersIds       = User::where('inviter', $inviterId)->get()->pluck('id');
        $usersTotal     = User::where('inviter', $inviterId)->count();
        $comissaoTotal  = Wallet::whereIn('user_id', $usersIds)->sum('refer_rewards');

        return [
            Stat::make('Balance to Receive', \Helper::amountFormatDecimal($comissaoTotal))
                ->description('Amount to receive')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),
            Stat::make('Available Balance', \Helper::amountFormatDecimal(0))
                ->description('Available balance for withdrawal')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),
            Stat::make('Registrations', $usersTotal)
                ->description('Users registered with my link')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),
        ];
    }

    /**
     * @return bool
     */
    public static function canView(): bool
    {
        return auth()->user()->hasRole('afiliado');
    }
}
