<?php

namespace App\Filament\Pages;

use App\Models\GamesKey;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Support\Exceptions\Halt;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;

class GamesKeyPage extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.games-key-page';

    protected static ?string $title = 'Game Keys';

    protected static ?string $slug = 'chaves-dos-jogos';

    /**
     * Hide from navigation - simplified admin panel
     */
    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    /**
     * @dev @victormsalatiel
     * @return bool
     */
    public static function canAccess(): bool
    {
        return auth()->user()->hasRole('admin');
    }


    public ?array $data = [];
    public ?GamesKey $setting;

    /**
     * @return void
     */
    public function mount(): void
    {
        $gamesKey = GamesKey::first();
        if(!empty($gamesKey)) {
            $this->setting = $gamesKey;
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
                Section::make('Games2Api')
                    ->description('Games2Api credentials')
                    ->schema([
                        TextInput::make('games2_agent_code')
                            ->label('Agent Code')
                            ->placeholder('Type the Agent Code here')
                            ->maxLength(191),
                        TextInput::make('games2_agent_token')
                            ->label('Agent Token')
                            ->placeholder('Type the Agent Token here')
                            ->maxLength(191),
                        TextInput::make('games2_agent_secret_key')
                            ->label('Agent Secret Key')
                            ->placeholder('Type the Agent Secret Key here')
                            ->maxLength(191),
                        TextInput::make('games2_api_endpoint')
                            ->label('Api Endpoint')
                            ->placeholder('Type the API endpoint here')
                            ->maxLength(191)
                            ->columnSpanFull(),
                    ])
                    ->columns(3),

                Section::make('World Slot API')
                    ->description('World Slot credentials')
                    ->schema([
                        TextInput::make('worldslot_agent_code')
                            ->label('Agent Code')
                            ->placeholder('Type the Agent Code here')
                            ->maxLength(191),
                        TextInput::make('worldslot_agent_token')
                            ->label('Agent Token')
                            ->placeholder('Type the Agent Token here')
                            ->maxLength(191),
                        TextInput::make('worldslot_agent_secret_key')
                            ->label('Agent Secret Key')
                            ->placeholder('Type the Agent Secret Key here')
                            ->maxLength(191),
                        TextInput::make('worldslot_api_endpoint')
                            ->label('Api Endpoint')
                            ->placeholder('Type the API endpoint here')
                            ->maxLength(191)
                            ->columnSpanFull(),
                    ])
                    ->columns(3),

                Section::make('Slotegrator API')
                    ->description('Slotegrator credentials')
                    ->schema([
                        TextInput::make('merchant_url')
                            ->label('Merchant URL')
                            ->placeholder('Type the API URL here')
                            ->maxLength(191),
                        TextInput::make('merchant_id')
                            ->label('Merchant ID')
                            ->placeholder('Type the Merchant ID here')
                            ->maxLength(191),
                        TextInput::make('merchant_key')
                            ->placeholder('Type the Merchant Key here')
                            ->label('Merchant Key')
                            ->maxLength(191),
                    ])
                    ->columns(3),



                Section::make('Salsa API')
                    ->description('Salsa credentials. Provider site: https://salsatechnology.com/')
                    ->schema([
                        TextInput::make('salsa_base_uri')
                            ->label('Salsa URI')
                            ->placeholder('Type the Salsa base URL here')
                            ->maxLength(191),
                        TextInput::make('salsa_pn')
                            ->label('Salsa PN')
                            ->placeholder('Type the PN here')
                            ->maxLength(191),
                        TextInput::make('salsa_key')
                            ->label('Salsa Key')
                            ->placeholder('Type the Salsa Key here')
                            ->maxLength(191),
                    ])
                    ->columns(3),

                Section::make('Vibra Gaming API')
                    ->description('Vibra Gaming credentials. Provider site: https://vibragaming.com/')
                    ->schema([
                        TextInput::make('vibra_site_id')
                            ->label('Vibra Site ID')
                            ->placeholder('Type the Vibra Site ID here')
                            ->maxLength(191),
                        TextInput::make('vibra_game_mode')
                            ->label('Vibra Game Mode')
                            ->placeholder('Type the Vibra game mode')
                            ->maxLength(191),
                    ])
                    ->columns(2),

                Section::make('Fivers API')
                    ->description('Fivers credentials')
                    ->schema([
                        TextInput::make('agent_code')
                            ->label('Agent Code')
                            ->placeholder('Type the Agent Code here')
                            ->maxLength(191),
                        TextInput::make('agent_token')
                            ->label('Agent Token')
                            ->placeholder('Type the Agent Token here')
                            ->maxLength(191),
                        TextInput::make('agent_secret_key')
                            ->label('Agent Secret Key')
                            ->placeholder('Type the Agent Secret Key here')
                            ->maxLength(191),
                        TextInput::make('api_endpoint')
                            ->label('Api Endpoint')
                            ->placeholder('Type the API endpoint here')
                            ->maxLength(191)
                            ->columnSpanFull(),
                    ])
                    ->columns(3),

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

            $setting = GamesKey::first();
            if(!empty($setting)) {
                if($setting->update($this->data)) {
                    Notification::make()
                        ->title('Keys updated')
                        ->body('Your keys were updated successfully!')
                        ->success()
                        ->send();
                }
            }else{
                if(GamesKey::create($this->data)) {
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
