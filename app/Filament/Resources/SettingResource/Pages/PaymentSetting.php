<?php

namespace App\Filament\Resources\SettingResource\Pages;

use App\Filament\Resources\SettingResource;
use App\Models\Setting;
use AymanAlhattami\FilamentPageWithSidebar\Traits\HasPageSidebar;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Filament\Support\Exceptions\Halt;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class PaymentSetting extends Page implements HasForms
{
    use HasPageSidebar, InteractsWithForms;

    protected static string $resource = SettingResource::class;

    protected static string $view = 'filament.resources.setting-resource.pages.payment-setting';

    /**
     * @return string|Htmlable
     */
    public function getTitle(): string | Htmlable
    {
        return __('Payments');
    }

    public Setting $record;
    public ?array $data = [];

    /**
     * @dev @victormsalatiel
     * @param Model $record
     * @return bool
     */
    public static function canView(Model $record): bool
    {
        return auth()->user()->hasRole('admin');
    }

    /**
     * @dev victormsalatiel - Meu instagram
     * @return void
     */
    public function mount(): void
    {
        $setting = Setting::first();
        $this->record = $setting;
        $this->form->fill($setting->toArray());
    }

    /**
     * @dev victormsalatiel - Meu instagram
     * @return void
     */
    public function save()
    {
        try {
            if(env('APP_DEMO')) {
                Notification::make()
                    ->title('Warning')
                    ->body('You cannot make this change in the demo version')
                    ->danger()
                    ->send();
                return;
            }

            $setting = Setting::find($this->record->id);

            if($setting->update($this->data)) {
                Cache::put('setting', $setting);

                Notification::make()
                    ->title('Saved')
                    ->body('Saved successfully!')
                    ->success()
                    ->send();

                /// Re-hydrate from the saved row: the upload fields are plain
                /// path strings right after the update and Filament's file
                /// component needs the array shape it builds while hydrating.
                $this->form->fill($setting->refresh()->toArray());

                // No redirect after saving: Livewire is still morphing the
                // form when it fires, which threw in the browser. The toast
                // says it saved and the page stays where it is.
            }
        } catch (Halt $exception) {
            return;
        }
    }

    /**
     * @dev victormsalatiel - Meu instagram
     * @param Form $form
     * @return Form
     */
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Fee settings')
                    ->description('Platform fees')
                    ->schema([
                        TextInput::make('min_deposit')
                            ->label('Min deposit')
                            ->numeric()
                            ->maxLength(191),
                        TextInput::make('max_deposit')
                            ->label('Max deposit')
                            ->numeric()
                            ->maxLength(191),
                        TextInput::make('min_withdrawal')
                            ->label('Min withdrawal')
                            ->numeric()
                            ->maxLength(191),
                        TextInput::make('max_withdrawal')
                            ->label('Max withdrawal')
                            ->numeric()
                            ->maxLength(191),
                        TextInput::make('initial_bonus')
                            ->label('Welcome bonus (%)')
                            ->numeric()
                            ->suffix('%')
                            ->maxLength(191),
                        TextInput::make('currency_code')
                            ->label('Currency code')
                            ->helperText('Three letters, for example USD or EUR. Crypto prices are quoted in it.')
                            ->maxLength(191),
                        TextInput::make('prefix')
                            ->label('Currency symbol')
                            ->helperText('Shown next to every amount, for example $ or €.')
                            ->maxLength(5),
//                        Select::make('decimal_format')->options([
//                            'dot' => 'Dot',
//                        ]),
//                        Select::make('currency_position')->options([
//                            'left' => 'Left',
//                            'right' => 'Right',
//                        ]),

                        Group::make()
                            ->label('Sub-affiliate percentage')
                            ->schema([
                            TextInput::make('perc_sub_lv1')
                                ->label('Sub-affiliate LV1 (%)')
                                ->numeric()
                                ->maxLength(191),
                            TextInput::make('perc_sub_lv2')
                                ->label('Sub-affiliate LV2 (%)')
                                ->numeric()
                                ->maxLength(191),
                            TextInput::make('perc_sub_lv3')
                                ->label('Sub-affiliate LV3 (%)')
                                ->numeric()
                                ->maxLength(191),
                        ])->columnSpanFull()->columns(3),
                        // SuitPay, Stripe and BSPay came with the script, are Brazil-only
                        // and have no credentials here. Crypto is set up under
                        // Financial > Payment Gateway.
                        Toggle::make('disable_spin')
                            ->label('Spin wheel for new visitors')
                            ->helperText('Off by default: the wheel that came with the script shows invented winners.')
                        ,
                    ])->columns(2)
            ])
            ->statePath('data') ;
    }
}
