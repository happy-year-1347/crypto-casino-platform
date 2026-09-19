<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MissionResource\Pages;
use App\Filament\Resources\MissionResource\RelationManagers;
use App\Models\Currency;
use App\Models\GameProvider;
use App\Models\Mission;
use App\Models\Provider;
use App\Models\Wallet;
use Filament\Forms;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class MissionResource extends Resource
{
    protected static ?string $model = Mission::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-group';

    protected static ?string $navigationLabel = 'Missions';

    protected static ?string $modelLabel = 'Missions';

    protected static ?string $slug = 'centro-missoes';

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
                    Forms\Components\TextInput::make('challenge_name')
                        ->required()
                        ->label('Mission Name')
                        ->placeholder('Enter mission name')
                        ->columnSpanFull()
                        ->maxLength(191),
                    RichEditor::make('challenge_description')
                        ->label('Description')
                        ->columnSpanFull()
                        ->placeholder('Enter mission description'),

                    RichEditor::make('challenge_rules')
                        ->label('Rules')
                        ->columnSpanFull()
                        ->placeholder('Enter mission rules'),
                    Select::make('challenge_type')
                        ->default('game')
                        ->label('Mission Type')
                        ->options([
                            'game' => 'Game',
                            'wallet' => 'Wallet',
                            'deposit' => 'Deposit',
                            'affiliate' => 'Affiliate',
                        ]),
                    Forms\Components\TextInput::make('challenge_link')
                        ->label('Mission Link')
                        ->maxLength(191),
                    Forms\Components\DateTimePicker::make('challenge_start_date')
                        ->label('Mission Start Date')
                        ->required(),
                    Forms\Components\DateTimePicker::make('challenge_end_date')
                        ->label('Mission End Date')
                        ->required(),
                    Forms\Components\TextInput::make('challenge_bonus')
                        ->label('Bonus value')
                        ->required()
                        ->numeric()
                        ->default(0.00),
                    Forms\Components\TextInput::make('challenge_total')
                        ->label('Total Missions')
                        ->required()
                        ->numeric()
                        ->default(1),
                    Select::make('challenge_currency')
                        ->label('Default Currency')
                        ->required()
                        ->options(Currency::all()->pluck('code', 'id'))
                        ->reactive()
                        ->default(Wallet::where('active', 1)->first()->currency)
                        ->searchable(),
                    Select::make('challenge_provider')
                        ->label('Provider')
                        ->options(Provider::all()->pluck('name', 'id'))
                        ->reactive()
                        ->searchable(),
                    Forms\Components\TextInput::make('challenge_gameid')
                        ->label('Game ID')
                        ->placeholder('Enter game ID, you can find it in the games list')
                        ->columnSpanFull()
                        ->maxLength(191),
                ])->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('challenge_name')
                    ->label('Name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('challenge_type')
                    ->label('Type')
                    ->searchable(),
                Tables\Columns\TextColumn::make('challenge_link')
                    ->label('Link')
                    ->searchable(),
                Tables\Columns\TextColumn::make('challenge_start_date')
                    ->label('Start Date')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('challenge_end_date')
                    ->label('End Date')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('challenge_bonus')
                    ->label('Prize Value')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('challenge_total')
                    ->label('Total')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('challenge_currency')
                    ->label('Currency')
                    ->searchable(),
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
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    /**
     * @return string[]
     */
    public static function getRelations(): array
    {
        return [
            RelationManagers\UsersRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMissions::route('/'),
            'create' => Pages\CreateMission::route('/create'),
            'edit' => Pages\EditMission::route('/{record}/edit'),
        ];
    }
}
