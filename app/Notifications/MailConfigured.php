<?php

namespace App\Notifications;

/**
 * Is there a mail account to send through?
 *
 * Without one, Laravel's mail channel throws on every notification. The bell in
 * the admin should not depend on that, so notifications ask here before adding
 * the mail channel.
 */
class MailConfigured
{
    public static function check(): bool
    {
        $mailer = (string) config('mail.default');

        if ($mailer === '' || $mailer === 'log' || $mailer === 'array') {
            // these need no credentials and never throw
            return $mailer === 'log' || $mailer === 'array';
        }

        if ($mailer === 'smtp') {
            return trim((string) config('mail.mailers.smtp.host')) !== '';
        }

        // any other transport the operator configures deliberately
        return true;
    }
}
