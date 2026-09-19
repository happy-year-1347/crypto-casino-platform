<?php

namespace App\Filament\Resources\GameResource\Pages;

use App\Filament\Resources\GameResource;
use App\Traits\Providers\Games2ApiTrait;
use App\Traits\Providers\WorldSlotTrait;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions\Action;




class ListGames extends ListRecords
{
    use WorldSlotTrait, Games2ApiTrait;

    /**
     * @var string
     */
    protected static string $resource = GameResource::class;

    /**
     * @return array|Action[]|\Filament\Actions\ActionGroup[]
     */
    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->icon('heroicon-o-plus')
                ->label('New game')
            ,

//            Action::make('loadingGame2Api')
//                ->label('Games2Api provider')
//                ->icon('heroicon-o-puzzle-piece')
//                ->color('info')
//                ->requiresConfirmation()
//                ->action(fn () => self::LoadingProviderGames2Api())
//            ,

//           Action::make('loadingGame2Api')
//                ->label('Games2Api games')
//                ->icon('heroicon-o-puzzle-piece')
//                ->color('success')
//                ->requiresConfirmation()
//                ->action(fn () => self::LoadingGames2Api()),

//            Action::make('loadingGame')
//                ->label('Carregar World Slot')
//                ->icon('heroicon-o-puzzle-piece')
//                ->color('success')
//                ->requiresConfirmation()
//                ->action(fn () => self::LoadingGames())
        ];
    }

    /**
     * Carregar todos os provedores
     * @dev victormsalatiel - Corra de golpista, me chame no instagram
     * @return void
     */
    protected static function LoadingProviderGames2Api()
    {
        self::GetAllProvidersGames2Api();
    }

    /**
     * Carregar todos os jogos
     * @dev victormsalatiel - Corra de golpista, me chame no instagram
     * @return void
     */
    protected static function LoadingGames2Api()
    {
        self::GetAllGamesGames2Api();
    }

    protected static function LoadingGames()
    {
        self::LoadingGamesWorldSlot();
    }
}
