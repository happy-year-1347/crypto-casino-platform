<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DepositResource\Pages;
use App\Filament\Resources\DepositResource\RelationManagers;
use App\Models\Deposit;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DepositResource extends Resource
{
    protected static ?string $model = Deposit::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-down-tray';

    protected static ?string $navigationLabel = 'Deposits';

    protected static ?string $modelLabel = 'Deposits';

    protected static ?string $navigationGroup = 'Administration';

    protected static ?string $slug = 'todos-depositos';

    protected static ?int $navigationSort = 2;

    /**
     * @dev @victormsalatiel
     * @return bool
     */
    public static function canAccess(): bool
    {
        return auth()->user()->hasRole('admin');
    }

    /**
     * @return string|null
     */
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', 0)->count();
    }

    /**
     * @return string|array|null
     */
    public static function getNavigationBadgeColor(): string|array|null
    {
        return static::getModel()::where('status', 0)->count() > 5 ? 'success' : 'warning';
    }

    /**
     * @param Form $form
     * @return Form
     */
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Deposit Registration')
                ->schema([
                    Forms\Components\Select::make('user_id')
                        ->label('Users')
                        ->placeholder('Select a user')
                        ->options(
                            fn($get) => User::query()
                                ->pluck('name', 'id')
                        )
                        ->searchable()
                        ->preload()
                        ->live()
                        ->required(),
                    Forms\Components\TextInput::make('amount')
                        ->label('Amount')
                        ->required()
                        ->default(0.00),
                    Forms\Components\TextInput::make('type')
                        ->label('Type')
                        ->required()
                        ->maxLength(191),
                    Forms\Components\FileUpload::make('proof')
                        ->label('Proof')
                        ->placeholder('Upload proof image')
                        ->image()
                        ->columnSpanFull()
                        ->required(),
                    Forms\Components\Toggle::make('status')
                        ->required(),
                ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('payment_id')
                    ->label('Payment ID')
                    ->searchable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('amount')
                    ->label('Amount')
                    ->formatStateUsing(fn (Deposit $record): string => $record->symbol . ' ' . $record->amount)
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('type')
                    ->label('Method')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => strtoupper((string) $state))
                    ->color(fn (?string $state): string => $state === 'crypto' ? 'warning' : 'gray')
                    ->searchable(),
                Tables\Columns\TextColumn::make('currency')
                    ->label('Coin')
                    ->formatStateUsing(fn (Deposit $record): string => $record->type === 'crypto' ? strtoupper((string) $record->currency) : '')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('crypto_amount')
                    ->label('Crypto amount')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('crypto_actually_paid')
                    ->label('Actually paid')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('crypto_address')
                    ->label('Pay address')
                    ->copyable()
                    ->limit(18)
                    ->tooltip(fn (Deposit $record): ?string => $record->crypto_address)
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('crypto_status')
                    ->label('Provider status')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => strtoupper((string) $state))
                    ->color(fn (?string $state): string => match ($state) {
                        'finished', 'confirmed' => 'success',
                        'failed', 'expired', 'refunded' => 'danger',
                        'partially_paid' => 'warning',
                        null, '' => 'gray',
                        default => 'info',
                    })
                    ->toggleable(),
                Tables\Columns\TextColumn::make('proof')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn ($state): string => match ((int) $state) {
                        1 => 'CREDITED',
                        2 => 'FAILED',
                        default => 'PENDING',
                    })
                    ->color(fn ($state): string => match ((int) $state) {
                        1 => 'success',
                        2 => 'danger',
                        default => 'warning',
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->label('Method')
                    ->options(['crypto' => 'Crypto', 'pix' => 'PIX']),
                Tables\Filters\SelectFilter::make('status')
                    ->options([0 => 'Pending', 1 => 'Credited', 2 => 'Failed']),
            ])
            ->actions([
                Tables\Actions\Action::make('crypto_refresh')
                    ->label('Check provider')
                    ->icon('heroicon-o-arrow-path')
                    ->color('gray')
                    ->visible(fn (Deposit $record): bool => $record->type === 'crypto' && (int) $record->status === 0)
                    ->action(function (Deposit $record) {
                        try {
                            $service = app(\App\Services\Crypto\CryptoDepositService::class);
                            $payment = app(\App\Services\Crypto\NowPaymentsClient::class)->getPayment($record->payment_id);
                            $status  = strtolower((string) ($payment['payment_status'] ?? 'waiting'));
                            $service->applyStatus($record, $status, $payment);
                            \Filament\Notifications\Notification::make()
                                ->title('Provider status: ' . strtoupper($status))
                                ->body((int) $record->fresh()->status === 1 ? 'The deposit has been credited.' : 'Not credited yet.')
                                ->info()
                                ->send();
                        } catch (\Throwable $e) {
                            \Filament\Notifications\Notification::make()->title('Check failed')->body($e->getMessage())->danger()->send();
                        }
                    }),
                Tables\Actions\Action::make('crypto_credit')
                    ->label('Credit manually')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (Deposit $record): bool => $record->type === 'crypto' && (int) $record->status === 0)
                    ->requiresConfirmation()
                    ->modalDescription(fn (Deposit $record) => 'Credits ' . $record->symbol . ' ' . $record->amount . ' to the player with the normal bonus and rollover rules. Use it for partially paid deposits after checking the amount received in NOWPayments.')
                    ->action(function (Deposit $record) {
                        $ok = app(\App\Services\Wallet\DepositFinalizer::class)->finalize($record->payment_id);
                        \Filament\Notifications\Notification::make()
                            ->title($ok ? 'Deposit credited' : 'Nothing to credit')
                            ->{$ok ? 'success' : 'warning'}()
                            ->send();
                    }),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make(),
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
            'index' => Pages\ListDeposits::route('/'),
            'create' => Pages\CreateDeposit::route('/create'),
            'edit' => Pages\EditDeposit::route('/{record}/edit'),
        ];
    }
}
