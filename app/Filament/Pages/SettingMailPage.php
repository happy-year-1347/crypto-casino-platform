<?php

namespace App\Filament\Pages;

use App\Models\SettingMail;
use Filament\Actions\Action;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Exceptions\Halt;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\HtmlString;
use Jackiedo\DotenvEditor\Facades\DotenvEditor;

class SettingMailPage extends Page
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-envelope';

    protected static string $view = 'filament.pages.setting-mail-page';

    protected static ?string $navigationLabel = 'E-mail';

    protected static ?string $title = 'E-mail';

    protected static ?string $slug = 'setting-mail-page';

    /**
     * This server cannot use the usual mail ports: the hosting company blocks
     * 25, 465 and 587 on the way out. 2525 and 2465 are open, so the settings
     * offered here use those. Anything on 587 will simply time out, which is
     * what makes a wrong setting look like "e-mail does not work".
     */
    public const OPEN_PORTS  = [2525, 2465];
    public const BLOCKED_PORTS = [25, 465, 587];

    protected const PROVIDERS = [
        'brevo' => [
            'label'      => 'Brevo',
            'host'       => 'smtp-relay.brevo.com',
            'port'       => '2525',
            'encryption' => 'tls',
        ],
        'smtp2go' => [
            'label'      => 'SMTP2GO',
            'host'       => 'mail.smtp2go.com',
            'port'       => '2525',
            'encryption' => 'tls',
        ],
        'resend' => [
            'label'      => 'Resend',
            'host'       => 'smtp.resend.com',
            'port'       => '2465',
            'encryption' => 'tls',
        ],
        'custom' => [
            'label' => 'Something else',
        ],
    ];

    public ?array $data = [];
    public SettingMail $setting;

    public static function canAccess(): bool
    {
        return auth()->user()->hasRole('admin');
    }

    public function mount(): void
    {
        $smtp = SettingMail::first();
        if (!empty($smtp)) {
            $this->setting = $smtp;
            $this->form->fill($this->setting->toArray());
        } else {
            $this->form->fill(['software_smtp_type' => 'smtp']);
        }
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Where the e-mail goes out through')
                    ->description('The site needs a sending service to send password resets and copies of the deposit and withdrawal alerts. This is not a mailbox, and it does not replace the support address on the site.')
                    ->schema([
                        Placeholder::make('port_warning')
                            ->hiddenLabel()
                            ->columnSpanFull()
                            ->content(new HtmlString(
                                '<div style="border-left:3px solid #f59e0b;padding:.5rem .75rem;background:rgba(245,158,11,.08)">'
                                . '<strong>Read this first.</strong> This server cannot use ports 25, 465 or 587: the hosting company blocks them, '
                                . 'so those settings time out no matter which company you sign up with. Ports <strong>2525</strong> and <strong>2465</strong> '
                                . 'do work. Pick a service below and the right port is filled in for you, then press '
                                . '<strong>Send a test e-mail</strong> before saving.'
                                . '</div>'
                            )),

                        Select::make('provider_preset')
                            ->label('Service')
                            ->options(collect(self::PROVIDERS)->map(fn ($p) => $p['label'])->all())
                            ->dehydrated(false)
                            ->live()
                            ->afterStateUpdated(function (Set $set, ?string $state): void {
                                $preset = self::PROVIDERS[$state] ?? null;
                                if (!$preset || !isset($preset['host'])) {
                                    return;
                                }
                                $set('software_smtp_type', 'smtp');
                                $set('software_smtp_mail_host', $preset['host']);
                                $set('software_smtp_mail_port', $preset['port']);
                                $set('software_smtp_mail_encryption', $preset['encryption']);
                            })
                            ->helperText('Choosing one fills in the address and the port that works from this server.'),

                        TextInput::make('software_smtp_mail_host')
                            ->label('Server address')
                            ->placeholder('smtp-relay.brevo.com')
                            ->maxLength(191),

                        TextInput::make('software_smtp_mail_port')
                            ->label('Port')
                            ->numeric()
                            ->placeholder('2525')
                            ->maxLength(191)
                            ->helperText('2525 or 2465. Anything else is blocked from this server.')
                            ->rules(['not_in:' . implode(',', self::BLOCKED_PORTS)])
                            ->validationMessages([
                                'not_in' => 'That port is blocked by the hosting company and will never connect. Use 2525, or 2465 for Resend.',
                            ]),

                        TextInput::make('software_smtp_mail_username')
                            ->label('Login')
                            ->placeholder('the login the service gives you')
                            ->maxLength(191),

                        TextInput::make('software_smtp_mail_password')
                            ->label('Key or password')
                            ->password()
                            ->placeholder('the key the service gives you')
                            ->maxLength(191),

                        TextInput::make('software_smtp_mail_encryption')
                            ->label('Encryption')
                            ->placeholder('tls')
                            ->maxLength(191),

                        TextInput::make('software_smtp_type')
                            ->label('Mailer')
                            ->placeholder('smtp')
                            ->maxLength(191)
                            ->helperText('Leave this as smtp.'),
                    ])->columns(3),

                Section::make('What players see it come from')
                    ->schema([
                        TextInput::make('software_smtp_mail_from_address')
                            ->label('From address')
                            ->email()
                            ->placeholder('support@vpcasino.net')
                            ->maxLength(191)
                            ->helperText('Use an address at your own domain, or the service will refuse it.'),

                        TextInput::make('software_smtp_mail_from_name')
                            ->label('From name')
                            ->placeholder('Viper Casino')
                            ->maxLength(191),
                    ])->columns(2),
            ])
            ->statePath('data');
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('sendTest')
                ->label('Send a test e-mail')
                ->icon('heroicon-o-paper-airplane')
                ->color('warning')
                ->form([
                    TextInput::make('to')
                        ->label('Send it to')
                        ->email()
                        ->required()
                        ->placeholder('your own e-mail address')
                        ->helperText('Uses whatever is typed in the form above, so you can test before saving.'),
                ])
                ->action(fn (array $data) => $this->sendTest($data['to'])),
        ];
    }

    /**
     * Sends one message with whatever is currently in the form, and reports the
     * real reason when it fails instead of a general "could not save".
     */
    public function sendTest(string $to): void
    {
        $host       = trim((string) ($this->data['software_smtp_mail_host'] ?? ''));
        $port       = (int) ($this->data['software_smtp_mail_port'] ?? 0);
        $username   = (string) ($this->data['software_smtp_mail_username'] ?? '');
        $password   = (string) ($this->data['software_smtp_mail_password'] ?? '');
        $encryption = trim((string) ($this->data['software_smtp_mail_encryption'] ?? ''));
        $from       = trim((string) ($this->data['software_smtp_mail_from_address'] ?? ''));
        $fromName   = trim((string) ($this->data['software_smtp_mail_from_name'] ?? '')) ?: config('app.name');

        if ($host === '' || $port === 0) {
            $this->failed('Fill in the server address and the port first.');
            return;
        }

        if ($from === '') {
            $this->failed('Fill in the from address first. The service will refuse a message without one.');
            return;
        }

        /// check the door is open before blaming the credentials
        $socket = @fsockopen($host, $port, $errNo, $errStr, 10);
        if ($socket === false) {
            if (in_array($port, self::BLOCKED_PORTS, true)) {
                $this->failed('Port ' . $port . ' is blocked by the hosting company, so nothing can go out on it. Use 2525, or 2465 for Resend.');
            } else {
                $this->failed('Could not reach ' . $host . ' on port ' . $port . '. ' . ($errStr ?: 'It did not answer.'));
            }
            return;
        }
        fclose($socket);

        config([
            'mail.mailers.probe' => [
                'transport'  => 'smtp',
                'host'       => $host,
                'port'       => $port,
                'encryption' => $encryption !== '' ? $encryption : null,
                'username'   => $username !== '' ? $username : null,
                'password'   => $password !== '' ? $password : null,
                'timeout'    => 20,
            ],
        ]);

        try {
            Mail::mailer('probe')->raw(
                "This is a test from your casino admin panel.\n\nIf you are reading this, the site can send e-mail: password resets will reach your players, and you will get a copy of the deposit and withdrawal alerts.\n\n" . config('app.name'),
                function ($message) use ($to, $from, $fromName) {
                    $message->to($to)->subject('Test from ' . config('app.name'))->from($from, $fromName);
                }
            );
        } catch (\Throwable $e) {
            $this->failed($this->explain($e->getMessage()));
            return;
        }

        Notification::make()
            ->title('Sent')
            ->body('The message went out to ' . $to . '. If it is not there in a minute, look in the spam folder, and remember these settings still need saving.')
            ->success()
            ->persistent()
            ->send();
    }

    /** Turns the usual SMTP complaints into something readable. */
    protected function explain(string $message): string
    {
        $map = [
            'authentication failed'  => 'The service refused the login. Check the login and the key.',
            'Expected response code' => 'The service refused the message. Most often the from address is not one it has approved yet.',
            'Connection could not be established' => 'The server could not open the connection. Check the address and the port.',
            'Unable to connect'      => 'The server could not open the connection. Check the address and the port.',
            'certificate'            => 'The secure connection failed. Try tls, or leave the encryption box empty.',
        ];

        foreach ($map as $needle => $friendly) {
            if (stripos($message, $needle) !== false) {
                return $friendly . ' (' . \Illuminate\Support\Str::limit($message, 160) . ')';
            }
        }

        return \Illuminate\Support\Str::limit($message, 240);
    }

    protected function failed(string $body): void
    {
        Notification::make()
            ->title('It did not go out')
            ->body($body)
            ->danger()
            ->persistent()
            ->send();
    }

    public function submit(): void
    {
        try {
            if (env('APP_DEMO')) {
                Notification::make()
                    ->title('Warning')
                    ->body('You cannot make this change in the demo version')
                    ->danger()
                    ->send();
                return;
            }

            $this->form->validate();

            $setting = SettingMail::first();
            if (!empty($setting)) {
                if (!empty($this->data['software_smtp_type'])) {
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

                    /// The .env change only counts once the cached config is out of the way.
                    /// Rebuilding it here ran config:cache inside the request, which
                    /// re-bootstrapped the container mid-render and left the form dying
                    /// on "Undefined variable $errors". Deleting the file is enough.
                    if (app()->configurationIsCached()) {
                        @unlink(app()->getCachedConfigPath());
                    }
                }

                if ($setting->update($this->data)) {
                    $this->setting = $setting->refresh();
                    $this->form->fill($this->setting->toArray());

                    Notification::make()
                        ->title('Saved')
                        ->body('The site will use these from now on. Send yourself a test if you have not already.')
                        ->success()
                        ->send();
                }
            } else {
                if ($created = SettingMail::create($this->data)) {
                    $this->setting = $created;
                    Notification::make()
                        ->title('Saved')
                        ->body('The site will use these from now on. Send yourself a test if you have not already.')
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
