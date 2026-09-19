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

class BonusSetting extends Page implements HasForms
{
    use HasPageSidebar, InteractsWithForms;

    protected static string $resource = SettingResource::class;

    protected static string $view = 'filament.resources.setting-resource.pages.bonus-setting';

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
        return __('VIP bonus');
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
                Section::make('Bonus settings')
                    ->description('Platform bonus')
                    ->schema([
                        TextInput::make('bonus_vip')
                            ->label('VIP bonus')
                            ->placeholder('VIP bonus earned for each 1 deposited.')
                            ->numeric()
                            ->helperText('VIP bonus given for each 1 deposited. Example: a deposit of 1 gives the player this amount of VIP bonus.')
                            ->maxLength(191),
                        Toggle::make('activate_vip_bonus')
                            ->inline(false)
                            ->label('Enable/disable VIP bonus'),
                    ])->columns(2)
            ])
            ->statePath('data') ;
    }
}
