<?php

namespace App\Livewire;

use App\Models\AffiliateHistory;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestAdminComissions extends BaseWidget
{
    protected static ?string $heading = 'My Affiliations';

    protected static ?int $navigationSort = -1;

    protected int | string | array $columnSpan = 'full';

    /**
     * @param Table $table
     * @return Table
     */
    public function table(Table $table): Table
    {
        return $table
            ->query(AffiliateHistory::query()->where('inviter', auth()->id()))
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('User'),
                Tables\Columns\TextColumn::make('user.email')
                    ->label('Email'),

                Tables\Columns\TextColumn::make('commission_type')
                    ->label('Type'),
                Tables\Columns\TextColumn::make('commission_paid')
                    ->money('BRL')
                    ->label('Commission'),
                Tables\Columns\TextColumn::make('losses_amount')
                    ->money('BRL')
                    ->badge()
                    ->color('danger')
                    ->label('Losses'),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pendente' => 'warning',
                        'pago' => 'success',
                    }),
                Tables\Columns\TextColumn::make('dateHumanReadable')
                    ->label('Date'),
                Tables\Columns\TextColumn::make('losses')
                    ->label('Total Losses')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ]);
    }

    /**
     * @return bool
     */
    public static function canView(): bool
    {
        return auth()->user()->hasRole('afiliado');
    }

}
