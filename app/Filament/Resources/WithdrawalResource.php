<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WithdrawalResource\Pages;
use App\Filament\Resources\WithdrawalResource\RelationManagers;
use App\Models\User;
use App\Models\Withdrawal;
use App\Services\Crypto\CryptoPayoutService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class WithdrawalResource extends Resource
{

    protected static ?string $model = Withdrawal::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-up-tray';

    protected static ?string $navigationLabel = 'Withdrawals';

    protected static ?string $modelLabel = 'Withdrawals';

    protected static ?string $navigationGroup = 'Administration';

    protected static ?string $slug = 'todos-saques';

    protected static ?int $navigationSort = 3;

    /**
     * @dev @victormsalatiel
     * @return bool
     */
    public static function canAccess(): bool
    {
        return auth()->user()->hasRole('admin');
    }

    /**
     * @return string[]
     */
    public static function getGloballySearchableAttributes(): array
    {
        return ['type', 'bank_info', 'user.name', 'user.last_name', 'user.cpf', 'user.phone',  'user.email'];
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
                Forms\Components\Section::make('Withdrawal Registration')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->label('Users')
                            ->placeholder('Select a user')
                            ->relationship(name: 'user', titleAttribute: 'name')
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

    /**
     * @param Table $table
     * @return Table
     */
    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Name')
                    ->searchable(['users.name', 'users.last_name'])
                    ->sortable(),
                Tables\Columns\TextColumn::make('amount')
                    ->label('Amount')
                    ->formatStateUsing(fn (Withdrawal $record): string => $record->symbol . ' ' . $record->amount)
                    ->sortable(),
                Tables\Columns\TextColumn::make('type')
                    ->label('Method')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => strtoupper((string) $state))
                    ->color(fn (?string $state): string => $state === 'crypto' ? 'warning' : 'gray'),
                Tables\Columns\TextColumn::make('crypto_currency')
                    ->label('Coin')
                    ->formatStateUsing(fn (?string $state): string => strtoupper((string) $state))
                    ->toggleable(),
                Tables\Columns\TextColumn::make('crypto_address')
                    ->label('Address')
                    ->copyable()
                    ->limit(18)
                    ->tooltip(fn (Withdrawal $record): ?string => $record->crypto_address)
                    ->toggleable(),
                Tables\Columns\TextColumn::make('crypto_amount')
                    ->label('Crypto amount')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('crypto_status')
                    ->label('Payout status')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => strtoupper((string) $state))
                    ->color(fn (?string $state): string => match ($state) {
                        'finished' => 'success',
                        'failed', 'rejected' => 'danger',
                        null, '' => 'gray',
                        default => 'warning',
                    })
                    ->toggleable(),
                Tables\Columns\TextColumn::make('crypto_tx_hash')
                    ->label('Tx hash')
                    ->copyable()
                    ->limit(14)
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('pix_type')
                    ->label('Pix type')
                    ->formatStateUsing(fn (?string $state): string => \Helper::formatPixType((string) $state))
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('pix_key')
                    ->label('Pix Key')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('bank_info')
                    ->label('Bank Information')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('proof')
                    ->label('Proof')
                    ->html()
                    ->formatStateUsing(fn (?string $state): string => $state ? '<a href="'.url('storage/'.$state).'" target="_blank">Download</a>' : '')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn ($state): string => match ((int) $state) {
                        1 => 'PAID',
                        2 => 'CANCELED',
                        default => 'PENDING',
                    })
                    ->color(fn ($state): string => match ((int) $state) {
                        1 => 'success',
                        2 => 'danger',
                        default => 'warning',
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Date')
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
                    ->options(['crypto' => 'Crypto', 'pix' => 'PIX', 'bank' => 'Bank']),
                Tables\Filters\SelectFilter::make('status')
                    ->options([0 => 'Pending', 1 => 'Paid', 2 => 'Canceled']),
            ])
            ->actions([
                /*
                 * Crypto withdrawals (NOWPayments payouts)
                 */
                Tables\Actions\ActionGroup::make([
                Action::make('crypto_send')
                    ->label('Send via NOWPayments')
                    ->icon('heroicon-o-paper-airplane')
                    ->color('success')
                    ->visible(fn (Withdrawal $w): bool => $w->type === 'crypto' && (int) $w->status === 0 && empty($w->crypto_batch_id))
                    ->form([
                        Forms\Components\Placeholder::make('summary')
                            ->label('Payout')
                            ->content(fn (Withdrawal $w) => \Helper::amountFormatDecimal($w->amount) . ' -> ' . strtoupper($w->crypto_currency) . ' to ' . $w->crypto_address),
                        Forms\Components\TextInput::make('code')
                            ->label('NOWPayments 2FA code')
                            ->helperText('6 digit code from the authenticator app linked to your NOWPayments account. The payout is created and confirmed in one step.')
                            ->required()
                            ->minLength(6)
                            ->maxLength(8),
                    ])
                    ->action(function (Withdrawal $w, array $data) {
                        try {
                            $result = app(CryptoPayoutService::class)->sendAndVerify($w, $data['code']);
                            Notification::make()
                                ->title('Payout sent')
                                ->body('Batch ' . $result['batch_id'] . ' created for ' . $result['crypto_amount'] . ' ' . strtoupper($w->crypto_currency) . '. Status updates arrive by IPN, or press Refresh.')
                                ->success()
                                ->send();
                        } catch (\Throwable $e) {
                            Notification::make()->title('Payout failed')->body($e->getMessage())->danger()->persistent()->send();
                        }
                    }),
                Action::make('crypto_verify')
                    ->label('Confirm 2FA')
                    ->icon('heroicon-o-shield-check')
                    ->color('warning')
                    ->visible(fn (Withdrawal $w): bool => $w->type === 'crypto' && (int) $w->status === 0 && !empty($w->crypto_batch_id) && in_array($w->crypto_status, ['creating', 'waiting', null, '']))
                    ->form([
                        Forms\Components\TextInput::make('code')->label('NOWPayments 2FA code')->required()->minLength(6)->maxLength(8),
                    ])
                    ->action(function (Withdrawal $w, array $data) {
                        try {
                            app(CryptoPayoutService::class)->verify($w, $data['code']);
                            Notification::make()->title('Payout confirmed')->success()->send();
                        } catch (\Throwable $e) {
                            Notification::make()->title('Confirmation failed')->body($e->getMessage())->danger()->persistent()->send();
                        }
                    }),
                Action::make('crypto_refresh')
                    ->label('Refresh')
                    ->icon('heroicon-o-arrow-path')
                    ->color('gray')
                    ->visible(fn (Withdrawal $w): bool => $w->type === 'crypto' && (int) $w->status === 0 && !empty($w->crypto_batch_id))
                    ->action(function (Withdrawal $w) {
                        try {
                            $status = app(CryptoPayoutService::class)->refresh($w);
                            Notification::make()->title('Payout status: ' . strtoupper($status))->info()->send();
                        } catch (\Throwable $e) {
                            Notification::make()->title('Refresh failed')->body($e->getMessage())->danger()->send();
                        }
                    }),
                Action::make('crypto_manual')
                    ->label('Mark paid manually')
                    ->icon('heroicon-o-check-circle')
                    ->color('info')
                    ->visible(fn (Withdrawal $w): bool => $w->type === 'crypto' && (int) $w->status === 0)
                    ->form([
                        Forms\Components\TextInput::make('tx_hash')->label('Transaction hash (optional)')->maxLength(191),
                    ])
                    ->requiresConfirmation()
                    ->modalDescription('Use this after sending the coins from your own wallet. The player is not refunded.')
                    ->action(function (Withdrawal $w, array $data) {
                        try {
                            app(CryptoPayoutService::class)->markPaidManually($w, $data['tx_hash'] ?? null);
                            Notification::make()->title('Withdrawal marked as paid')->success()->send();
                        } catch (\Throwable $e) {
                            Notification::make()->title('Failed')->body($e->getMessage())->danger()->send();
                        }
                    }),
                Action::make('crypto_cancel')
                    ->label('Cancel & refund')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn (Withdrawal $w): bool => $w->type === 'crypto' && (int) $w->status === 0)
                    ->requiresConfirmation()
                    ->modalDescription(fn (Withdrawal $w) => 'The amount of ' . \Helper::amountFormatDecimal($w->amount) . ' goes back to the player\'s withdrawable balance.')
                    ->action(function (Withdrawal $w) {
                        try {
                            app(CryptoPayoutService::class)->cancel($w, 'canceled by admin');
                            Notification::make()->title('Withdrawal canceled and refunded')->success()->send();
                        } catch (\Throwable $e) {
                            Notification::make()->title('Failed')->body($e->getMessage())->danger()->send();
                        }
                    }),
                ])
                    ->label('Crypto payout')
                    ->icon('heroicon-o-currency-dollar')
                    ->color('warning')
                    ->button()
                    ->visible(fn (Withdrawal $w): bool => $w->type === 'crypto' && (int) $w->status === 0),

                /*
                 * PIX withdrawals (SuitPay)
                 */
                Action::make('deny_payment')
                    ->label('Cancel')
                    ->icon('heroicon-o-banknotes')
                    ->color('danger')
                    ->visible(fn (Withdrawal $withdrawal): bool => !$withdrawal->status && $withdrawal->type !== 'crypto')
                    ->action(function(Withdrawal $withdrawal) {
                        \Filament\Notifications\Notification::make()
                            ->title('Cancel Withdrawal')
                            ->success()
                            ->persistent()
                            ->body('You are canceling a withdrawal of '. \Helper::amountFormatDecimal($withdrawal->amount))
                            ->actions([
                                \Filament\Notifications\Actions\Action::make('view')
                                    ->label('Confirm')
                                    ->button()
                                    ->url(route('suitpay.cancelwithdrawal', ['id' => $withdrawal->id]))
                                    ->close(),
                                \Filament\Notifications\Actions\Action::make('undo')
                                    ->color('gray')
                                    ->label('Cancel')
                                    ->action(function(Withdrawal $withdrawal) {

                                    })
                                    ->close(),
                            ])
                            ->send();
                    }),
                Action::make('approve_payment')
                    ->label('Make payment')
                    ->icon('heroicon-o-banknotes')
                    ->color('success')
                    ->visible(fn (Withdrawal $withdrawal): bool => !$withdrawal->status && $withdrawal->type !== 'crypto')
                    ->action(function(Withdrawal $withdrawal) {
                        \Filament\Notifications\Notification::make()
                            ->title('Withdrawal')
                            ->success()
                            ->persistent()
                            ->body('You are requesting a withdrawal of '. \Helper::amountFormatDecimal($withdrawal->amount))
                            ->actions([
                                \Filament\Notifications\Actions\Action::make('view')
                                    ->label('Confirm')
                                    ->button()
                                    ->url(route('suitpay.withdrawal', ['id' => $withdrawal->id]))
                                    ->close(),
                                \Filament\Notifications\Actions\Action::make('undo')
                                    ->color('gray')
                                    ->label('Cancel')
                                    ->action(function(Withdrawal $withdrawal) {

                                    })
                                    ->close(),
                            ])
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



    /**
     * @return array|\Filament\Resources\RelationManagers\RelationGroup[]|\Filament\Resources\RelationManagers\RelationManagerConfiguration[]|string[]
     */
    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListWithdrawals::route('/'),
            'create' => Pages\CreateWithdrawal::route('/create'),
            'edit' => Pages\EditWithdrawal::route('/{record}/edit'),
        ];
    }
}
