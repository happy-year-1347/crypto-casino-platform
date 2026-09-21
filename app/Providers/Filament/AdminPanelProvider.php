<?php

namespace App\Providers\Filament;

use Althinect\FilamentSpatieRolesPermissions\FilamentSpatieRolesPermissionsPlugin;
use App\Filament\Pages\AdvancedPage;
use App\Filament\Pages\GamesKeyPage;
use App\Filament\Pages\GatewayPage;
use App\Filament\Pages\LayoutCssCustom;
use App\Filament\Pages\SettingMailPage;
use App\Filament\Pages\Settings;
use App\Filament\Pages\SettingSpin;
use App\Filament\Pages\SuitPayPaymentPage;
use App\Filament\Resources\AffiliateUserResource;
use App\Filament\Resources\AffiliateWithdrawResource;
use App\Filament\Resources\BannerResource;
use App\Filament\Resources\CategoryResource;
use App\Filament\Resources\DepositResource;
use App\Filament\Resources\GameResource;
use App\Filament\Resources\MissionResource;
use App\Filament\Resources\ProviderResource;
use App\Filament\Resources\SettingResource;
use App\Filament\Resources\SubAffiliateResource;
use App\Filament\Resources\UserResource;
use App\Filament\Resources\VipResource;
use App\Filament\Resources\WalletResource;
use App\Filament\Resources\WithdrawalResource;
use App\Livewire\AdminWidgets;
use App\Livewire\LatestAdminComissions;
use App\Livewire\WalletOverview;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationBuilder;
use Filament\Navigation\NavigationGroup;
use Filament\Navigation\NavigationItem;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use App\Filament\Pages\DashboardAdmin;

class AdminPanelProvider extends PanelProvider
{
    /**
     * @param Panel $panel
     * @return Panel
     */
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->loginRouteSlug('login')
            ->darkMode(true)
            ->colors([
                'danger' => Color::Red,
                'gray' => Color::Slate,
                'info' => Color::Blue,
                'primary' => Color::Indigo,
                'success' => Color::Emerald,
                'warning' => Color::Orange,
            ])

            ->font('Roboto Condensed')
            /// The bell in the header. Deposits and withdrawal requests were
            /// already being recorded, but with nothing to display them the
            /// owner only learned about them by e-mail, which needs a mail
            /// account set up first.
            ->databaseNotifications()
            ->databaseNotificationsPolling('30s')
            ->brandLogo(fn () => view('filament.components.logo'))
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                DashboardAdmin::class,
            ])
            ->globalSearchKeyBindings(['command+k', 'ctrl+k'])
            ->sidebarCollapsibleOnDesktop()
            ->collapsibleNavigationGroups(true)
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                WalletOverview::class,
                /// AdminWidgets showed CPA, revshare and losses for people the
                /// signed-in admin had personally referred, which for the owner is
                /// three zeros with invented trend lines under them. The dashboard
                /// is where he checks the takings, so it stays off it.
                LatestAdminComissions::class,
            ])
            ->navigation(function (NavigationBuilder $builder): NavigationBuilder {
                return $builder->groups([
                    NavigationGroup::make()
                        ->items([
                            NavigationItem::make('dashboard')
                                ->icon('heroicon-o-home')
                                ->label(fn (): string => __('filament-panels::pages/dashboard.title'))
                                ->url(fn (): string => DashboardAdmin::getUrl())
                                ->isActiveWhen(fn () => request()->routeIs('filament.pages.settings')),
                        ])
                    ,
                    
                    // User Management & Permissions - Consolidated
                    auth()->user()->hasRole('admin') ?
                        NavigationGroup::make(__('user.user_management'))
                            ->items([
                                ...UserResource::getNavigationItems(),
                                NavigationItem::make(__('filament-spatie-roles-permissions::filament-spatie.section.role'))
                                    ->icon('heroicon-o-shield-check')
                                    ->label(__('user.roles'))
                                    ->isActiveWhen(fn () => request()->routeIs([
                                        'filament.admin.resources.roles.index',
                                        'filament.admin.resources.roles.create',
                                        'filament.admin.resources.roles.view',
                                        'filament.admin.resources.roles.edit',
                                    ]))
                                    ->url(fn (): string => '/admin/roles'),
                                NavigationItem::make(__('filament-spatie-roles-permissions::filament-spatie.section.permission'))
                                    ->icon('heroicon-o-key')
                                    ->label(__('user.permissions'))
                                    ->isActiveWhen(fn () => request()->routeIs([
                                        'filament.admin.resources.permissions.index',
                                        'filament.admin.resources.permissions.create',
                                        'filament.admin.resources.permissions.view',
                                        'filament.admin.resources.permissions.edit',
                                    ]))
                                    ->url(fn (): string => '/admin/permissions'),
                                ...WalletResource::getNavigationItems(),
                            ])
                        : NavigationGroup::make()
                    ,

                    // Financial Operations
                    auth()->user()->hasRole('admin') ?
                        NavigationGroup::make(__('user.financial'))
                            ->items([
                                ...DepositResource::getNavigationItems(),
                                ...WithdrawalResource::getNavigationItems(),
                                NavigationItem::make('withdraw_affiliates')
                                    ->icon('heroicon-o-banknotes')
                                    ->label(__('user.affiliate_withdrawals'))
                                    ->url(fn (): string => AffiliateWithdrawResource::getUrl())
                                    ->isActiveWhen(fn () => request()->routeIs('filament.admin.resources.affiliate-withdraws.index')),
                                NavigationItem::make('gateway')
                                    ->icon('heroicon-o-credit-card')
                                    ->label(__('user.payment_gateway'))
                                    ->url(fn (): string => GatewayPage::getUrl())
                                    ->isActiveWhen(fn () => request()->routeIs('filament.pages.gateway-page')),
                            ])
                        : NavigationGroup::make()
                    ,

                    // Game Management
                    auth()->user()->hasRole('admin') ?
                        NavigationGroup::make(__('user.games'))
                            ->items([
                                ...CategoryResource::getNavigationItems(),
                                ...ProviderResource::getNavigationItems(),
                                ...GameResource::getNavigationItems(),
                            ])
                        : NavigationGroup::make()
                    ,

                    // Features & Modules
                    auth()->user()->hasRole('admin') ?
                        NavigationGroup::make(__('user.features'))
                            ->items([
                                ...MissionResource::getNavigationItems(),
                                ...VipResource::getNavigationItems(),
                                ...BannerResource::getNavigationItems(),
                                NavigationItem::make('custom-layout')
                                    ->icon('heroicon-o-paint-brush')
                                    ->label(__('user.customization'))
                                    ->url(fn (): string => LayoutCssCustom::getUrl())
                                    ->isActiveWhen(fn () => request()->routeIs('filament.pages.layout-css-custom')),
                            ])
                        : NavigationGroup::make()
                    ,

                    // Settings - Consolidated
                    auth()->user()->hasRole('admin') ?
                        NavigationGroup::make(__('user.settings'))
                            ->items([
                                NavigationItem::make('settings')
                                    ->icon('heroicon-o-cog-6-tooth')
                                    ->label(__('user.general'))
                                    ->url(fn (): string => SettingResource::getUrl())
                                    ->isActiveWhen(fn () => request()->routeIs('filament.admin.resources.settings.index')),
                                NavigationItem::make('games-key')
                                    ->icon('heroicon-o-key')
                                    ->label(__('user.game_keys'))
                                    ->url(fn (): string => GamesKeyPage::getUrl())
                                    ->isActiveWhen(fn () => request()->routeIs('filament.pages.games-key-page')),
                                NavigationItem::make('setting-spin')
                                    ->icon('heroicon-o-arrow-path')
                                    ->label(__('user.spin'))
                                    ->url(fn (): string => SettingSpin::getUrl())
                                    ->isActiveWhen(fn () => request()->routeIs('filament.pages.setting-spin')),
                                NavigationItem::make('setting-mail')
                                    ->icon('heroicon-o-envelope')
                                    ->label(__('user.email'))
                                    ->url(fn (): string => SettingMailPage::getUrl())
                                    ->isActiveWhen(fn () => request()->routeIs('filament.pages.setting-mail-page')),
                                NavigationItem::make('advanced_page')
                                    ->icon('heroicon-o-wrench-screwdriver')
                                    ->label(__('user.advanced'))
                                    ->url(fn (): string => AdvancedPage::getUrl())
                                    ->isActiveWhen(fn () => request()->routeIs('filament.pages.advanced-page')),
                            ])
                        : NavigationGroup::make()
                    ,

                    // Affiliate Section
                    auth()->user()->hasRole('afiliado') ?
                        NavigationGroup::make(__('user.affiliate'))
                            ->items([
                                NavigationItem::make('sub_affiliates')
                                    ->icon('heroicon-o-user-group')
                                    ->label(__('user.my_sub_affiliates'))
                                    ->url(fn (): string => SubAffiliateResource::getUrl())
                                    ->isActiveWhen(fn () => request()->routeIs('filament.admin.resources.sub-affiliates.index')),
                                NavigationItem::make('withdraw_affiliates')
                                    ->icon('heroicon-o-banknotes')
                                    ->label(__('user.my_withdrawals'))
                                    ->url(fn (): string => AffiliateWithdrawResource::getUrl())
                                    ->isActiveWhen(fn () => request()->routeIs('filament.admin.resources.affiliate-withdraws.index')),
                                NavigationItem::make('Invite Link')
                                    ->url(url('/register?code='.auth()->user()->inviter_code), shouldOpenInNewTab: true)
                                    ->icon('heroicon-o-link')
                                    ->label(__('user.invite_link')),
                            ])
                        : NavigationGroup::make()
                    ,

                    // Utilities
                    NavigationGroup::make(__('user.utilities'))
                        ->items([
                            NavigationItem::make('Clear Cache')
                                ->url(url('/clear'), shouldOpenInNewTab: false)
                                ->icon('heroicon-o-trash')
                                ->label(__('user.clear_cache')),
                        ])
                    ,
                ]);
            })
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->plugin(FilamentSpatieRolesPermissionsPlugin::make())
            ;
    }
}
