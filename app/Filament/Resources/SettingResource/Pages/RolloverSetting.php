<?php

namespace App\Filament\Resources\SettingResource\Pages;

use App\Filament\Resources\SettingResource;
use App\Models\Setting;
use AymanAlhattami\FilamentPageWithSidebar\Traits\HasPageSidebar;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Section;
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
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class RolloverSetting extends Page implements HasForms
{
    use HasPageSidebar, InteractsWithForms;

    protected static string $resource = SettingResource::class;

    protected static string $view = 'filament.resources.setting-resource.pages.rollover-setting';

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
     * @return string|Htmlable
     */
    public function getTitle(): string | Htmlable
    {
        return __('Rollover');
    }

    public Setting $record;
    public ?array $data = [];

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
                Section::make('Welcome bonus limits')
                    ->description('These are the numbers the Welcome Bonus page shows players, so changing one here changes the published terms with it.')
                    ->schema([
                        TextInput::make('bonus_max')
                            ->label('Most the bonus can pay')
                            ->numeric()
                            ->helperText('The cap, in site currency. Without one a 100% offer pays 100% of any deposit, however large. 0 removes the cap.')
                            ->maxLength(191),
                        TextInput::make('bonus_min_deposit')
                            ->label('Smallest deposit that earns it')
                            ->numeric()
                            ->helperText('Deposits below this get no bonus.')
                            ->maxLength(191),
                        TextInput::make('bonus_max_bet')
                            ->label('Largest bet while a bonus is live')
                            ->numeric()
                            ->helperText('Refused above this while bonus money is in play, so a rollover cannot be cleared in a few big spins. 0 removes the limit.')
                            ->maxLength(191),
                        TextInput::make('bonus_days')
                            ->label('Days before the bonus expires')
                            ->numeric()
                            ->suffix('days')
                            ->helperText('Whatever is left of the bonus goes after this. 0 means it never expires.')
                            ->maxLength(191),
                    ])->columns(2),

                Section::make('Rollover')
                    ->description('How many times money must be wagered')
                    ->schema([
                        TextInput::make('rollover_deposit')
                            ->label('Deposit rollover')
                            ->numeric()
                            ->default(1)
                            ->suffix('x')
                            ->helperText('How many times the deposit must be wagered. The published bonus terms say deposits are not wagered just for arriving alongside a bonus, so this should be 0 unless you change that wording.')
                            ->maxLength(191),
                        TextInput::make('rollover')
                            ->label('Bonus rollover')
                            ->numeric()
                            ->default(1)
                            ->suffix('x')
                            ->helperText('How many times the bonus must be wagered')
                            ->maxLength(191),
                    ])->columns(2)
            ])
            ->statePath('data') ;
    }
}
