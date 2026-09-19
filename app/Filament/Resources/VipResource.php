<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VipResource\Pages;
use App\Filament\Resources\VipResource\RelationManagers;
use App\Models\Vip;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class VipResource extends Resource
{
    protected static ?string $model = Vip::class;

    protected static ?string $navigationIcon = 'heroicon-o-trophy';

    protected static ?string $navigationLabel = 'Vip';

    protected static ?string $modelLabel = 'Vip';

    protected static ?string $slug = 'vip';

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

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Vip')
                    ->description('Register your VIP bonus list')
                    ->schema([
                        FileUpload::make('bet_symbol')
                            ->label('Symbol')
                            ->placeholder('Upload a symbol')
                            ->image()
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('bet_level')
                            ->label('Level')
                            ->required()
                            //->readOnly(fn (string $context): bool => $context === 'create')
                            ->placeholder('Keep the list in numerical order')
//                            ->default(function() {
//                                $ultimoRegistro = Vip::latest()->first();
//                                return $ultimoRegistro ? $ultimoRegistro->id + 1 : 1;
//                            })
                        ,
                        Forms\Components\TextInput::make('bet_required')
                            ->label('Required Bet')
                            ->required()
                            ->placeholder('Enter required bet to win the prize')
                            ->numeric(),
                        Forms\Components\Select::make('bet_period')
                            ->label('Period')
                            ->options([
                                'weekly' => 'Weekly',
                                'monthly' => 'Monthly',
                                'yearly' => 'Yearly',
                            ]),
                        Forms\Components\TextInput::make('bet_bonus')
                            ->label('Bonus')
                            ->placeholder('Enter total bonus')
                            ->required()
                            ->numeric(),
                    ])->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('bet_symbol')
                    ->label('Image'),
                Tables\Columns\TextColumn::make('bet_level')
                    ->label('Level')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('bet_required')
                    ->label('Required Bet')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('bet_period')
                    ->label('Period')
                    ->searchable(),
                Tables\Columns\TextColumn::make('bet_bonus')
                    ->label('Bonus')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Date')
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

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListVips::route('/'),
            'create' => Pages\CreateVip::route('/create'),
            'edit' => Pages\EditVip::route('/{record}/edit'),
        ];
    }
}
