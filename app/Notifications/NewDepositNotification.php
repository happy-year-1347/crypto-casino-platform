<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Filament\Notifications\Notification as FilamentNotification;

class NewDepositNotification extends Notification
{
    use Queueable;

    public $name;
    public $amout;

    public function __construct($name, $amout)
    {
        $this->name  = $name;
        $this->amout = $amout;
    }

    /**
     * The bell in the admin always gets it. E-mail is only attempted when a mail
     * account is actually set up, otherwise every deposit threw and left a
     * warning in the log.
     */
    public function via(object $notifiable): array
    {
        return MailConfigured::check() ? ['database', 'mail'] : ['database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)->view(
            'emails.new-deposit', ['usuario' => $this->name, 'valor' => \Helper::amountFormatDecimal($this->amout)]
        );
    }

    /**
     * The bell reads Filament's own shape, not a plain array, so build it
     * with Filament's builder. Written any other way the panel just says
     * there is nothing to show.
     */
    public function toDatabase(object $notifiable): array
    {
        return FilamentNotification::make()
            ->title('New deposit')
            ->body($this->name . ' deposited ' . \Helper::amountFormatDecimal($this->amout))
            ->icon('heroicon-o-arrow-down-tray')
            ->success()
            ->getDatabaseMessage();
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title'  => 'New deposit',
            'body'   => $this->name . ' deposited ' . \Helper::amountFormatDecimal($this->amout),
            'icon'   => 'heroicon-o-arrow-down-tray',
            'status' => 'success',
            // the older records used this key, keep it so they still display
            'message' => $this->name . ' deposited ' . \Helper::amountFormatDecimal($this->amout),
        ];
    }
}
