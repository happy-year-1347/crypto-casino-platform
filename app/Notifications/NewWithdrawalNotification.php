<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Filament\Notifications\Notification as FilamentNotification;

class NewWithdrawalNotification extends Notification
{
    use Queueable;

    /**
     * @var $name
     */
    public $name;

    /**
     * @var $amout
     */
    public $amout;

    /**
     * Create a new notification instance.
     */
    public function __construct($name, $amout)
    {
        $this->name  = $name;
        $this->amout = $amout;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    /**
     * The bell in the admin always gets it. E-mail is only attempted when a
     * mail account is actually set up.
     */
    public function via(object $notifiable): array
    {
        return MailConfigured::check() ? ['database', 'mail'] : ['database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)->view(
            'emails.new-withdrawal', ['usuario' => $this->name, 'valor' => \Helper::amountFormatDecimal($this->amout)]
        );
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    /**
     * The bell reads Filament's own shape, not a plain array, so build it
     * with Filament's builder. Written any other way the panel just says
     * there is nothing to show.
     */
    public function toDatabase(object $notifiable): array
    {
        return FilamentNotification::make()
            ->title('Withdrawal requested')
            ->body($this->name . ' asked to withdraw ' . \Helper::amountFormatDecimal($this->amout))
            ->icon('heroicon-o-arrow-up-tray')
            ->warning()
            ->getDatabaseMessage();
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title'  => 'Withdrawal requested',
            'body'   => $this->name . ' asked to withdraw ' . \Helper::amountFormatDecimal($this->amout),
            'icon'   => 'heroicon-o-arrow-up-tray',
            'status' => 'warning',
            // the older records used this key, keep it so they still display
            'message' => $this->name . ' asked to withdraw ' . \Helper::amountFormatDecimal($this->amout),
        ];
    }
}
