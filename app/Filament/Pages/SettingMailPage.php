<?php

namespace App\Filament\Pages;

use App\Models\SettingMail;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Exceptions\Halt;
use Illuminate\Database\Eloquent\Model;
use Jackiedo\DotenvEditor\Facades\DotenvEditor;

class SettingMailPage extends Page
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.setting-mail-page';

    /**
     * Hide from navigation - simplified admin panel
     */
    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    public ?array $data = [];
    public SettingMail $setting;

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
        $smtp = SettingMail::first();
        if(!empty($smtp)) {
            $this->setting = $smtp;
            $this->form->fill($this->setting->toArray());
        }else{
            $this->form->fill();
        }
    }

    /**
     * @param Form $form
     * @return Form
     */
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('SMTP')
                    ->description('SMTP credentials settings')
                    ->schema([
                        TextInput::make('software_smtp_type')
                            ->label('Mailer')
                            ->placeholder('Enter mailer (smtp)')
                            ->maxLength(191),
                        TextInput::make('software_smtp_mail_host')
                            ->label('Host')
                            ->placeholder('Enter your mail host')
                            ->maxLength(191),
                        TextInput::make('software_smtp_mail_port')
                            ->label('Port')
                            ->placeholder('Enter port')
                            ->maxLength(191),
                        TextInput::make('software_smtp_mail_username')
                            ->label('User')
                            ->placeholder('Enter user')
                            ->maxLength(191),
                        TextInput::make('software_smtp_mail_password')
                            ->label('Password')
                            ->placeholder('Enter password')
                            ->maxLength(191),
                        TextInput::make('software_smtp_mail_encryption')
                            ->label('Encryption')
                            ->placeholder('Enter encryption')
                            ->maxLength(191),
                        TextInput::make('software_smtp_mail_from_address')
                            ->label('From Email Address')
                            ->placeholder('Enter from email address')
                            ->maxLength(191),
                        TextInput::make('software_smtp_mail_from_name')
                            ->label('From Name')
                            ->placeholder('Enter from name')
                            ->maxLength(191)
                    ])->columns(4),
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
                    ->title('Warning')
                    ->body('You cannot make this change in the demo version')
                    ->danger()
                    ->send();
                return;
            }

            $setting = SettingMail::first();
            if(!empty($setting)) {
                if(!empty($this->data['software_smtp_type'])) {
                    $envs = DotenvEditor::load(base_path('.env'));

                    $envs->setKeys([
                        'MAIL_MAILER' => $this->data['software_smtp_type'],
                        'MAIL_HOST' => $this->data['software_smtp_mail_host'],
                        'MAIL_PORT' => $this->data['software_smtp_mail_port'],
                        'MAIL_USERNAME' => $this->data['software_smtp_mail_username'],
                        'MAIL_PASSWORD' => $this->data['software_smtp_mail_password'],
                        'MAIL_ENCRYPTION' => $this->data['software_smtp_mail_encryption'],
                        'MAIL_FROM_ADDRESS' => $this->data['software_smtp_mail_from_address'],
                        'MAIL_FROM_NAME' => $this->data['software_smtp_mail_from_name'],
                    ]);

                    $envs->save();

                    /// the config is cached in production, rebuild it or this save does nothing
                    if (app()->configurationIsCached()) {
                        \Artisan::call('config:cache');
                    }
                }

                if($setting->update($this->data)) {
                    Notification::make()
                        ->title('Keys updated')
                        ->body('Your keys were updated successfully!')
                        ->success()
                        ->send();
                }
            }else{
                if(SettingMail::create($this->data)) {
                    Notification::make()
                        ->title('Keys created')
                        ->body('Your keys were created successfully!')
                        ->success()
                        ->send();
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
