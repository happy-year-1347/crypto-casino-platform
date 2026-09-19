<?php

namespace App\Filament\Pages;

use App\Models\Gateway;
use App\Models\Setting;
use App\Services\Crypto\NowPaymentsClient;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Exceptions\Halt;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\HtmlString;

class GatewayPage extends Page
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';

    protected static string $view = 'filament.pages.gateway-page';

    protected static ?string $navigationLabel = 'Crypto Payment';

    protected static ?string $title = 'Crypto Payment Settings';

    protected static ?string $slug = 'crypto-payment';

    public ?array $data = [];
    public Gateway $setting;

    /**
     * @dev @victormsalatiel
     * @return bool
     */
    public static function canAccess(): bool
    {
        return auth()->user()->hasRole('admin');
    }

    /**
     * @return void
     */
    public function mount(): void
    {
        $gateway = Gateway::first();
        if(!empty($gateway)) {
            $this->setting = $gateway;
            $data = $this->setting->toArray();
            $data['crypto_payout_password'] = ''; // never echo the stored secret back to the form
            $data['crypto_currencies'] = $data['crypto_currencies'] ?: array_keys(NowPaymentsClient::DEFAULT_CURRENCIES);
            $this->form->fill($data);
        }else{
            $this->form->fill([
                'crypto_currencies' => array_keys(NowPaymentsClient::DEFAULT_CURRENCIES),
            ]);
        }
    }

    /**
     * @param Form $form
     * @return Form
     */
    public function form(Form $form): Form
    {
        $currencyOptions = collect(NowPaymentsClient::DEFAULT_CURRENCIES)
            ->map(fn ($meta) => $meta['label'])
            ->all();

        return $form
            ->schema([
                Section::make('Cryptocurrency (NOWPayments)')
                    ->description('Deposits and withdrawals in BTC, LTC, USDT, TRX, DOGE and more through nowpayments.io. Coins settle to the payout wallets configured in your NOWPayments dashboard.')
                    ->schema([
                        Toggle::make('crypto_is_enabled')
                            ->label('Enable crypto cashier')
                            ->helperText('Shows the crypto option on the deposit and withdrawal pages')
                            ->columnSpanFull(),
                        Toggle::make('crypto_sandbox')
                            ->label('Sandbox mode')
                            ->helperText('Use api-sandbox.nowpayments.io with a sandbox API key for testing. Turn off for real money.')
                            ->columnSpanFull(),
                        TextInput::make('crypto_api_key')
                            ->label('API Key')
                            ->placeholder('Enter your NOWPayments API key')
                            ->helperText('NOWPayments Dashboard > Store settings > API keys')
                            ->maxLength(191)
                            ->columnSpanFull(),
                        TextInput::make('crypto_webhook_secret')
                            ->label('IPN Secret Key')
                            ->placeholder('Enter your IPN secret')
                            ->helperText('NOWPayments Dashboard > Store settings > IPN. Every callback is checked against this key.')
                            ->maxLength(191)
                            ->columnSpanFull(),
                        Placeholder::make('ipn_urls')
                            ->label('Callback URLs (set these in the NOWPayments dashboard)')
                            ->content(fn () => new HtmlString(
                                '<div class="text-sm"><div>Deposits: <code>' . e(route('crypto.webhook')) . '</code></div>' .
                                '<div>Payouts: <code>' . e(route('crypto.payout.webhook')) . '</code></div></div>'
                            ))
                            ->columnSpanFull(),
                        CheckboxList::make('crypto_currencies')
                            ->label('Coins offered to players')
                            ->options($currencyOptions)
                            ->columns(3)
                            ->helperText('Only coins that are also enabled in your NOWPayments account will be shown.')
                            ->columnSpanFull(),
                        Toggle::make('crypto_fee_paid_by_user')
                            ->label('Player pays the network fee')
                            ->helperText('When on, the quoted crypto amount includes the NOWPayments fee so you receive the full deposit.')
                            ->columnSpanFull(),
                    ]),

                Section::make('Withdrawals (payouts)')
                    ->description('Withdrawals can be sent straight from your NOWPayments custody balance. This needs the account login and the 2FA code from the NOWPayments authenticator at the moment of sending. You can also mark a withdrawal as paid after sending it from your own wallet.')
                    ->schema([
                        TextInput::make('crypto_payout_email')
                            ->label('NOWPayments account e-mail')
                            ->email()
                            ->maxLength(191),
                        TextInput::make('crypto_payout_password')
                            ->label('NOWPayments account password')
                            ->password()
                            ->autocomplete('new-password')
                            ->helperText('Stored encrypted. Leave blank to keep the current password.')
                            ->maxLength(191),
                    ])->columns(2),
            ])
            ->statePath('data');
    }

    /**
     * @return void
     */
    public function submit(): void
    {
        try {
            if(env('APP_DEMO')) {
                Notification::make()
                    ->title('Attention')
                    ->body('You cannot make changes in demo mode')
                    ->danger()
                    ->send();
                return;
            }

            $data = $this->data;

            if(empty($data['crypto_payout_password'])) {
                unset($data['crypto_payout_password']);
            }else{
                $data['crypto_payout_password'] = NowPaymentsClient::encryptSecret($data['crypto_payout_password']);
            }

            $data['crypto_currencies'] = array_values(array_filter((array) ($data['crypto_currencies'] ?? [])));

            $setting = Gateway::first();
            $saved = !empty($setting) ? $setting->update($data) : (bool) Gateway::create($data);

            if($saved) {
                /// the player side reads the flag from settings, keep both in sync
                Setting::query()->update(['crypto_is_enabled' => (int) !empty($data['crypto_is_enabled'])]);
                Cache::forget('setting');
                Cache::flush();

                Notification::make()
                    ->title('Settings Updated')
                    ->body('Your crypto payment settings have been saved.')
                    ->success()
                    ->send();

                $this->data['crypto_payout_password'] = '';
            }

        } catch (Halt $exception) {
            Notification::make()
                ->title('Error')
                ->body('Failed to save settings!')
                ->danger()
                ->send();
        }
    }

    /**
     * Ping NOWPayments with the saved key so the admin knows the credentials work.
     */
    public function testConnection(): void
    {
        $client = new NowPaymentsClient(Gateway::first());

        if(!$client->apiKey()) {
            Notification::make()->title('Save an API key first')->warning()->send();
            return;
        }

        if($client->status()) {
            $coins = implode(', ', array_map('strtoupper', $client->merchantCoins()));
            Notification::make()
                ->title('NOWPayments connection OK')
                ->body('Coins enabled in your account: ' . ($coins ?: 'none'))
                ->success()
                ->send();
        }else{
            Notification::make()
                ->title('NOWPayments connection failed')
                ->body('Check the API key and the sandbox toggle.')
                ->danger()
                ->send();
        }
    }
}
