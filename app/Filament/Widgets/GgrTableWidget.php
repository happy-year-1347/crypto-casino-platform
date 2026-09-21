<?php

namespace App\Filament\Widgets;

use App\Models\GGRGamesFiver;
use App\Models\GgrGamesWorldSlot;
use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;
use Filament\Tables;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class GgrTableWidget extends BaseWidget
{

    protected static ?string $heading = 'GGR World Slot';

    protected static ?int $navigationSort = -1;

    protected int | string | array $columnSpan = 'full';

    /**
     * @param Table $table
     * @return Table
     */
    public function table(Table $table): Table
    {
        return $table
            ->query(GgrGamesWorldSlot::query())
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('User'),
                Tables\Columns\TextColumn::make('provider')
                    ->label('Provider')
                    ->searchable(),
                Tables\Columns\TextColumn::make('game')
                    ->label('Game')
                    ->searchable(),
                Tables\Columns\TextColumn::make('balance_bet')
                    ->money('BRL')
                    ->label('Bet Balance'),
                Tables\Columns\TextColumn::make('balance_win')
                    ->money('BRL')
                    ->label('Win Balance'),
                Tables\Columns\TextColumn::make('dateHumanReadable')
                    ->label('Date')
            ])
            ->filters([
                Filter::make('created_at')
                    ->form([
                        DatePicker::make('created_from')->label('Start Date'),
                        DatePicker::make('created_until')->label('End Date'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];

                        if ($data['created_from'] ?? null) {
                            $indicators['created_from'] = 'Start Date ' . Carbon::parse($data['created_from'])->toFormattedDateString();
                        }

                        if ($data['created_until'] ?? null) {
                            $indicators['created_until'] = 'End Date ' . Carbon::parse($data['created_until'])->toFormattedDateString();
                        }

                        return $indicators;
                    }),
            ])
            ;
    }


    /**
     * @return bool
     */
    public static function canView(): bool
    {
        /// the "GGR World Slot" table belongs to the same unused third party
        /// provider as GGROverview; see the note there.
        return false;
    }
}
