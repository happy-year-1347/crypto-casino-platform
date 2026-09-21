<!DOCTYPE html>
{{-- Goes to a player, so it follows the language they are using the site in.
     The strings live in lang/<code>.json next to the rest of the site's text. --}}
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <title>{{ __('Reset your password') }}</title>
</head>
<body style="margin: 0; padding: 0; background-color: #6C7A89;">
<table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="background-color: #6C7A89;">
    <tr>
        <td style="text-align: center; padding: 50px;">
            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="max-width: 600px; margin: 0 auto;">
                <tr>
                    <td style="background-color: #fff; padding: 24px; border-radius: 10px; box-shadow: 0 1px 4px rgba(0, 0, 0, 0.16); font-family: Arial, Helvetica, sans-serif; color: #1f2937; text-align: left;">
                        <h2 style="margin: 0 0 16px; font-size: 20px;">{{ __('Reset your password') }}</h2>

                        <p style="margin: 0 0 16px; font-size: 15px; line-height: 1.5;">
                            {{ __('Someone asked to reset the password for your :site account. If it was you, use the button below.', ['site' => config('app.name')]) }}
                        </p>

                        <p style="margin: 0 0 20px;">
                            <a href="{{ $resetLink }}" style="background-color: #16a34a; color: #fff; padding: 12px 22px; text-decoration: none; border-radius: 6px; display: inline-block; font-size: 15px;">{{ __('Choose a new password') }}</a>
                        </p>

                        <p style="margin: 0 0 8px; font-size: 13px; color: #6b7280;">
                            {{ __('If the button does not work, copy this address into your browser:') }}
                        </p>
                        <p style="margin: 0 0 20px; font-size: 13px; word-break: break-all;">
                            <a href="{{ $resetLink }}" style="color: #2563eb;">{{ $resetLink }}</a>
                        </p>

                        <p style="margin: 0 0 8px; font-size: 13px; color: #6b7280;">
                            {{ __('Or enter this code on the site:') }}
                        </p>
                        <p style="margin: 0 0 20px;">
                            <code style="padding: 8px 10px; background-color: #f3f4f6; border-radius: 4px; font-size: 14px; word-break: break-all; display: inline-block;">{{ $token }}</code>
                        </p>

                        <p style="margin: 0 0 8px; font-size: 14px; line-height: 1.5;">
                            {{ __('The link works for one hour and can only be used once.') }}
                        </p>
                        <p style="margin: 0; font-size: 14px; line-height: 1.5;">
                            {{ __('If you did not ask for this, you can ignore this message. Your password stays as it is.') }}
                        </p>

                        <p style="margin: 20px 0 0; font-size: 12px; color: #6b7280;">
                            {{ config('app.name') }}
                        </p>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>
</html>
