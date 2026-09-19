<?php

namespace App\Filament\Resources\UserResource\Widgets;

use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class UserStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $totalUsers = User::count();
        $activeUsers = User::where('status', 'active')->count();
        $suspendedUsers = User::where('status', 'suspended')->count();
        $bannedUsers = User::where('banned', 1)->count();
        $todayUsers = User::whereDate('created_at', today())->count();
        $verifiedUsers = User::whereNotNull('email_verified_at')->count();

        return [
            Stat::make(__('user.total_users'), $totalUsers)
                ->description(__('user.all_registered_users'))
                ->descriptionIcon('heroicon-o-users')
                ->color('primary'),
            
            Stat::make(__('user.active_users'), $activeUsers)
                ->description(round(($activeUsers / max($totalUsers, 1)) * 100, 1) . '% ' . __('user.of_total'))
                ->descriptionIcon('heroicon-o-check-circle')
                ->color('success'),
            
            Stat::make(__('user.suspended_users'), $suspendedUsers)
                ->description(__('user.suspended_accounts'))
                ->descriptionIcon('heroicon-o-pause-circle')
                ->color('warning'),
            
            Stat::make(__('user.blocked_users'), $bannedUsers)
                ->description(__('user.blocked_accounts'))
                ->descriptionIcon('heroicon-o-lock-closed')
                ->color('danger'),
            
            Stat::make(__('user.new_today'), $todayUsers)
                ->description(__('user.registrations_today'))
                ->descriptionIcon('heroicon-o-user-plus')
                ->color('info'),
            
            Stat::make(__('user.verified_emails'), $verifiedUsers)
                ->description(round(($verifiedUsers / max($totalUsers, 1)) * 100, 1) . '% ' . __('user.of_total'))
                ->descriptionIcon('heroicon-o-envelope-open')
                ->color('success'),
        ];
    }
}
