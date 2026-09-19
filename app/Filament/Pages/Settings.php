<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use AymanAlhattami\FilamentPageWithSidebar\FilamentPageSidebar;
use AymanAlhattami\FilamentPageWithSidebar\PageNavigationItem;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Support\Exceptions\Halt;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use App\Filament\Pages;
use Jackiedo\DotenvEditor\Facades\DotenvEditor;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Filament\Forms\Components\Actions\Action;


class Settings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static string $view = 'filament.pages.settings';

    protected static ?string $navigationLabel = 'Settings';

    protected static ?string $modelLabel = 'Settings';

    protected static ?string $title = 'Settings';

    // not 'settings': the settings resource already owns /admin/settings
    protected static ?string $slug = 'settings-all';

    /**
     * @dev @victormsalatiel
     * @return bool
     */
    public static function canAccess(): bool
    {
        return auth()->user()->hasRole('admin');
    }

    public ?array $data = [];
    public Setting $setting;

    /**
     * @dev victormsalatiel - Meu instagram
     * @return void
     */
    public function mount(): void
    {
        $this->setting = Setting::first();
        $this->form->fill($this->setting->toArray());
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
                Section::make('Site details')
                    ->schema([
                        TextInput::make('software_name')
                            ->label('Name')
                            ->required()
                            ->maxLength(191),
                        TextInput::make('software_description')
                            ->label('Description')
                            ->maxLength(191),
                        TextInput::make('support_email')
                            ->label('Support e-mail')
                            ->email()
                            ->helperText('Shown on the Support page. Leave empty to hide it.')
                            ->maxLength(191),
                        TextInput::make('support_telegram')
                            ->label('Support Telegram')
                            ->helperText('Full link, for example https://t.me/yourname. Leave empty to hide it.')
                            ->maxLength(191),
                    ])->columns(2),

                Section::make('Logos')
                    ->schema([
                        FileUpload::make('software_favicon')
                            ->label('Favicon')
                            ->placeholder('Upload a favicon')
                            ->image(),
                        FileUpload::make('software_logo_white')
                            ->label('Light logo')
                            ->placeholder('Upload a light logo')
                            ->image(),
                        FileUpload::make('software_logo_black')
                            ->label('Dark logo')
                            ->placeholder('Upload a dark logo')
                            ->image(),
                    ])->columns(3),

                Section::make('Background')
                    ->schema([
                        FileUpload::make('software_background')
                            ->label('Background')
                            ->placeholder('Upload a background')
                            ->image()
                        ->columnSpanFull(),
                    ]),

                Section::make('Deposits and withdrawals')
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
                        TextInput::make('rollover')
                            ->label('Rollover')
                            ->numeric()
                            ->maxLength(191),
                    ])->columns(5),

                Section::make('Football')
                    ->description('Football Settings')
                    ->schema([
                        TextInput::make('soccer_percentage')
                            ->label('Football commission (%)')
                            ->numeric()
                            ->suffix('%')
                            ->maxLength(191),

                        Toggle::make('turn_on_football')
                            ->inline(false)
                            ->label('Enable football'),
                    ])->columns(2),

                Section::make('Fees')
                    ->description('Platform Earnings Settings')
                    ->schema([
                        TextInput::make('revshare_percentage')
                            ->label('RevShare (%)')
                            ->numeric()
                            ->suffix('%')
                            ->maxLength(191),
                        Toggle::make('revshare_reverse')
                            ->inline(false)
                            ->label('Enable negative revshare')
                            ->helperText('Lets an affiliate carry a negative balance from the losses of the players they referred.')
                        ,
                        TextInput::make('ngr_percent')
                            ->label('NGR (%)')
                            ->numeric()
                            ->suffix('%')
                            ->maxLength(191),
                    ])->columns(3),
                Section::make('General')
                    ->schema([
                        TextInput::make('initial_bonus')
                            ->label('Welcome bonus (%)')
                            ->numeric()
                            ->suffix('%')
                            ->maxLength(191),
                        TextInput::make('currency_code')
                            ->label('Currency code')
                            ->helperText('Three letters, for example USD or EUR. Crypto prices follow this.')
                            ->maxLength(191),
                        TextInput::make('prefix')
                            ->label('Currency symbol')
                            ->helperText('Shown next to every amount, for example $ or €.')
                            ->maxLength(5),
                        Select::make('decimal_format')->options([
                            'dot' => 'Dot',
                        ]),
                        Select::make('currency_position')->options([
                            'left' => 'Left',
                            'right' => 'Right',
                        ]),
                        Toggle::make('disable_spin')
                            ->label('Disable Spin')
                        ,
                    ])->columns(4),
            ])
            ->statePath('data');
    }

    /**
     * @dev victormsalatiel - Meu instagram
     * @param array $data
     * @return array
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        return $data;
    }

    /**
     *
     * @dev victormsalatiel - Meu instagram
     * @return array
     */
    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label(__('Submit'))
                ->action(fn () => $this->submit())
                ->submit('submit')
            //->url(route('filament.admin.pages.dashboard'))
            ,
        ];
    }

    /**
     * @dev victormsalatiel - Meu instagram
     * @param $array
     * @return mixed|void
     */
    private function uploadFile($array)
    {
        if(!empty($array) && is_array($array) || !empty($array) && is_object($array)) {
            foreach ($array as $k => $temporaryFile) {
                if ($temporaryFile instanceof TemporaryUploadedFile) {
                    $path = \Helper::upload($temporaryFile);
                    if($path) {
                        return $path['path'];
                    }
                }else{
                    return $temporaryFile;
                }
            }
        }
    }


    /**
     * @dev victormsalatiel - Meu instagram
     * @return void
     */
    public function submit(): void
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


            $setting = Setting::first();

            if(!empty($setting)) {

                $favicon   = $this->data['software_favicon'];
                $logoWhite = $this->data['software_logo_white'];
                $logoBlack = $this->data['software_logo_black'];
                $softwareBackground = $this->data['software_background'];

                if (is_array($softwareBackground) || is_object($softwareBackground)) {
                    if(!empty($softwareBackground)) {
                        $this->data['software_background'] = $this->uploadFile($softwareBackground);
                    }
                }

                if (is_array($favicon) || is_object($favicon)) {
                    if(!empty($favicon)) {
                        $this->data['software_favicon'] = $this->uploadFile($favicon);
                    }
                }

                if (is_array($logoWhite) || is_object($logoWhite)) {
                    if(!empty($logoWhite)) {
                        $this->data['software_logo_white'] = $this->uploadFile($logoWhite);
                    }
                }

                if (is_array($logoBlack) || is_object($logoBlack)) {
                    if(!empty($logoBlack)) {
                        $this->data['software_logo_black'] = $this->uploadFile($logoBlack);
                    }
                }

                $envs = DotenvEditor::load(base_path('.env'));

                $envs->setKeys([
                    'APP_NAME' => $this->data['software_name'],
                ]);

                $envs->save();

                /// the config is cached in production, rebuild it or this save does nothing
                if (app()->configurationIsCached()) {
                    \Artisan::call('config:cache');
                }

                if($setting->update($this->data)) {

                    Cache::put('setting', $setting);

                    Notification::make()
                        ->title('Saved')
                        ->body('Saved successfully!')
                        ->success()
                        ->send();

                    redirect(route('filament.admin.pages.dashboard-admin'));

                }
            }


        } catch (Halt $exception) {
            Notification::make()
                ->title('Could not save')
                ->body('Something went wrong while saving. Please try again.')
                ->danger()
                ->send();
        }
    }


}
