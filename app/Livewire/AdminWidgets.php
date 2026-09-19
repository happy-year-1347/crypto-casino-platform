<?php

namespace App\Livewire;

use App\Models\AffiliateHistory;
use App\Models\User;
use App\Models\Wallet;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AdminWidgets extends BaseWidget
{
    protected static ?int $navigationSort = -2;

    /**
     * @return array|Stat[]
     */
    protected function getCards(): array
    {
        if(auth()->check()) {
            $inviterId = auth()->user()->id;
            $usersIds = User::where('inviter', $inviterId)->get()->pluck('id');

            if(!empty($usersIds)) {
                $comissaoRevshare   = AffiliateHistory::whereIn('user_id', $usersIds)->where('commission_type', 'revshare')->sum('commission_paid');
                $comissaoCPAs       = AffiliateHistory::whereIn('user_id', $usersIds)->where('commission_type', 'cpa')->sum('commission_paid');
                $lossesRev          = AffiliateHistory::whereIn('user_id', $usersIds)->where('commission_type', 'revshare')->sum('losses_amount');
            }
        }else{
            $comissaoRevshare   = 0;
            $comissaoCPAs       = 0;
            $lossesRev       = 0;
        }

        return [
            Stat::make('CPA Commission', \Helper::amountFormatDecimal($comissaoCPAs))
                ->description('CPA Commission')
                ->descriptionIcon('heroicon-o-arrow-trending-up')
                ->icon('heroicon-o-gift')
                ->color('success')
                ->chart([5, 8, 10, 12, 15, 18, 20]),
            Stat::make('Revshare Commission', \Helper::amountFormatDecimal($comissaoRevshare))
                ->description('Revshare Commission')
                ->descriptionIcon('heroicon-o-arrow-trending-up')
                ->icon('heroicon-o-chart-bar-square')
                ->color('success')
                ->chart([10, 12, 15, 18, 20, 22, 25]),
            Stat::make('Losses', \Helper::amountFormatDecimal($lossesRev))
                ->description('Referred Losses')
                ->descriptionIcon('heroicon-o-arrow-trending-down')
                ->icon('heroicon-o-exclamation-triangle')
                ->color('danger')
                ->chart([25, 22, 20, 18, 15, 12, 10]),
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
