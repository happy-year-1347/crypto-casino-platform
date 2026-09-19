<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GameResource\Pages;
use App\Filament\Resources\GameResource\RelationManagers;
use App\Models\Category;
use App\Models\Game;
use App\Models\Provider;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class GameResource extends Resource
{
    protected static ?string $model = Game::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'All Games';

    protected static ?string $modelLabel = 'All Games';

    /**
     * Hide from navigation - simplified admin panel
     */
    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    /**
     * @dev @victormsalatiel
     * @return bool
     */
    public static function canAccess(): bool
    {
        return auth()->user()->hasRole('admin');
    }

    /**
     * @param Form $form
     * @return Form
     */
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
               Forms\Components\Section::make('')
                ->schema([
                    Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Select::make('provider_id')
                            ->label('Provider')
                            ->placeholder('Select a provider')
                            ->relationship(name: 'provider', titleAttribute: 'name')
                            ->options(
                                fn($get) => Provider::query()
                                    ->pluck('name', 'id')
                            )
                            ->searchable()
                            ->preload()
                            ->live()
                            ->columnSpanFull()
                        ,
                        Forms\Components\Select::make('categories')
                            ->label('Category')
                            ->placeholder('Select categories for your game')
                            ->multiple()
                            ->relationship('categories', 'name')
                            ->searchable()
                            ->preload()
                            ->live()
                            ->columnSpanFull()
                        ,
                        Forms\Components\TextInput::make('game_server_url')
                            ->label('Server URL')
                            ->placeholder('Add server URL if exists')
                            ->maxLength(191),
                        Forms\Components\TextInput::make('game_name')
                            ->label('Game Name')
                            ->placeholder('Enter game name')
                            ->required()
                            ->maxLength(191),
                        Forms\Components\Textarea::make('description')
                            ->label('Description')
                            ->placeholder('Enter game description')
                            ->autosize(),
                        Forms\Components\Grid::make()
                        ->schema([
                            Forms\Components\TextInput::make('game_id')
                                ->label('Game ID')
                                ->placeholder('Enter game ID')
                                ->required()
                                ->maxLength(191),
                            Forms\Components\TextInput::make('game_code')
                                ->placeholder('Enter game code')
                                ->label('Game Code')
                                ->required()
                                ->maxLength(191),
                            Forms\Components\TextInput::make('game_type')
                                ->placeholder('Enter game type')
                                ->label('Game Type')
                                ->required()
                                ->maxLength(191),
                        ])->columns(3),
                        Forms\Components\FileUpload::make('cover')
                            ->label('Cover')
                            ->placeholder('Upload game cover')
                            ->image()
                            ->columnSpanFull()
                            ->helperText('Recommended cover size is 322x322')
                            ->required(),
                    ]),
                    Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\TextInput::make('technology')
                            ->label('Technology')
                            ->placeholder('Enter game technology, example: html, java, construct 3')
                            ->maxLength(191),
                        Forms\Components\TextInput::make('rtp')
                            ->label('RTP')
                            ->placeholder('Enter game RTP')
                            ->required()
                            ->numeric()
                            ->default(90),
                        Forms\Components\Select::make('distribution')
                            ->label('Distribution')
                            ->placeholder('Select distribution')
                            ->required()
                            ->options(\Helper::getDistribution()),
                        Forms\Components\TextInput::make('views')
                            ->label('Views')
                            ->required()
                            ->numeric()
                            ->default(0),
                        Forms\Components\Toggle::make('has_lobby')
                            ->required(),
                        Forms\Components\Toggle::make('is_mobile')
                            ->required(),
                        Forms\Components\Toggle::make('has_freespins')
                            ->required(),
                        Forms\Components\Toggle::make('has_tables')
                            ->required(),
                        Forms\Components\Toggle::make('only_demo')
                            ->required(),
                        Forms\Components\Toggle::make('is_featured')
                            ->label('Featured'),
                        Forms\Components\Toggle::make('show_home')
                            ->label('Show on Home'),
                        Forms\Components\Toggle::make('status')
                            ->label('Status')
                            ->helperText('Turn the game on or off')
                            ->default(true)
                            ->required(),
                    ])

                ])->columns(2)
            ]);
    }

    /**
     * @param Table $table
     * @return Table
     * @throws \Exception
     */
    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\ImageColumn::make('cover')
                ->label('Capa')
                //->disk('media')
                ,
                Tables\Columns\TextColumn::make('provider.name')
                    ->label('Provider')
                    ->numeric()
                    ->sortable()
                ,
                Tables\Columns\TextColumn::make('categories.name')
                    ->label('Categories')
                    ->wrap()
                    ->badge()
                ,
                Tables\Columns\TextColumn::make('game_server_url')
                    ->label('Server URL')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                Tables\Columns\TextColumn::make('game_id')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('Game ID')
                    ->searchable(),
                Tables\Columns\TextColumn::make('game_name')
                    ->label('Name')
                    ->searchable(),
                Tables\Columns\ToggleColumn::make('show_home')
                    ->afterStateUpdated(function ($record, $state) {
                        if($state == 1) {
                            $record->update(['status' => 1]);
                        }
                    })
                    ->label('Show on home'),
                Tables\Columns\ToggleColumn::make('is_featured')
                    ->label('Featured'),
                Tables\Columns\TextColumn::make('game_code')
                    ->label('Code')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                Tables\Columns\TextColumn::make('game_type')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('Type')
                    ->searchable(),
                Tables\Columns\TextColumn::make('description')
                    ->label('Description')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                Tables\Columns\ToggleColumn::make('status')
                    ->label('Status'),
                Tables\Columns\TextColumn::make('technology')
                    ->label('Technology')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                Tables\Columns\IconColumn::make('has_lobby')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_mobile')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->boolean(),
                Tables\Columns\IconColumn::make('has_freespins')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->boolean(),
                Tables\Columns\IconColumn::make('has_tables')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->boolean(),
                Tables\Columns\ToggleColumn::make('only_demo')
                    ->label('Demo')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('rtp')
                    ->label('RTP')
                    ->suffix('%')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('distribution')
                    ->label('Distribution')
                    ->badge(),
                Tables\Columns\TextColumn::make('views')
                    ->icon('heroicon-o-eye')
                    ->numeric()
                    ->formatStateUsing(fn (Game $record): string => \Helper::formatNumber($record->views))
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('categories.name')
                    ->relationship('categories', 'name')
                    ->preload()
                    ->multiple()
                    ->indicator('Category')
                    ->searchable(),
//                SelectFilter::make('Categoria')
//                    ->relationship('category', 'name')
//                    ->label('Select a category')
//                    ->indicator('Category'),
                SelectFilter::make('provider_id')
                    ->relationship('provider', 'name')
                    ->label('Provider')
                    ->indicator('Provider'),
                SelectFilter::make('distribution')
                    ->label('Distribution')
                    ->options(\Helper::getDistribution())
                    ->attribute('distribution')
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkAction::make('Enable games')
                    ->icon('heroicon-m-check')
                    ->requiresConfirmation()
                    ->action(function($records) {
                        return $records->each->update(['status' => 1]);
                    }),
                Tables\Actions\BulkAction::make('Disable games')
                    ->icon('heroicon-m-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(function($records) {
                        return $records->each(function($record) {
                            $record->update(['status' => 0]);
                        });
                    }),
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListGames::route('/'),
            'create' => Pages\CreateGame::route('/create'),
            'edit' => Pages\EditGame::route('/{record}/edit'),
        ];
    }
}
