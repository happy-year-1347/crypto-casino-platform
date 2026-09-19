<?php

namespace App\Filament\Resources\UserResource\Widgets;

use App\Models\GGRGamesFiver;
use App\Models\Order;
use App\Models\User;
use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;
use Filament\Tables;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class MyBetsTableWidget extends BaseWidget
{

    protected static ?string $heading = 'All bets';

    protected static ?int $navigationSort = -1;

    protected int | string | array $columnSpan = 'full';

    public User $record;
    

    /**
     * @param Table $table
     * @return Table
     */
    public function table(Table $table): Table
    {
        // \Log::info('DADOS XXXXXXXXXXX ' . json_encode($this->record->id));

        return $table
            ->query(Order::query()->where('user_id', $this->record->id))
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('game')
                    ->label('Game')
                    ->searchable(),
                Tables\Columns\TextColumn::make('type')
                    ->label('Type')
                    ->badge()
                    ->searchable(),
                Tables\Columns\TextColumn::make('type_money')
                    ->label('Transaction type')
                    ->badge()
                    ->color('success')
                    ->searchable(),
                Tables\Columns\TextColumn::make('amount')
                    ->label('Price')
                    ->money('BRL')
                    ->searchable(),
                Tables\Columns\TextColumn::make('providers')
                    ->label('Provider')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Date')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Filter::make('created_at')
                    ->form([
                        DatePicker::make('created_from')->label('Start date'),
                        DatePicker::make('created_until')->label('End date'),
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
                            $indicators['created_from'] = 'Created from ' . Carbon::parse($data['created_from'])->toFormattedDateString();
                        }

                        if ($data['created_until'] ?? null) {
                            $indicators['created_until'] = 'Created until ' . Carbon::parse($data['created_until'])->toFormattedDateString();
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
        return auth()->user()->hasRole('admin');
    }
}
