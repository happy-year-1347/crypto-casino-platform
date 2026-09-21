<!DOCTYPE html>
{{-- Goes to the owner, who reads English, so this one is not translated. --}}
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>New deposit</title>
</head>
<body style="margin: 0; padding: 0; background-color: #6C7A89;">
<table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="background-color: #6C7A89;">
    <tr>
        <td style="text-align: center; padding: 50px;">
            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="max-width: 600px; margin: 0 auto;">
                <tr>
                    <td style="background-color: #fff; padding: 24px; border-radius: 10px; box-shadow: rgba(0, 0, 0, 0.16) 0px 1px 4px; font-family: Arial, Helvetica, sans-serif; color: #1f2937; text-align: left;">
                        <h2 style="margin: 0 0 16px; font-size: 20px;">New deposit</h2>
                        <p style="margin: 0 0 12px; font-size: 15px; line-height: 1.5;">
                            <strong>{{ $usuario }}</strong> deposited <strong>{{ $valor }}</strong>.
                        </p>
                        <p style="margin: 0 0 20px; font-size: 15px; line-height: 1.5;">
                            The money is already in the player's balance. You can see the payment under Deposits in the admin panel.
                        </p>
                        <a href="{{ url('/admin') }}" style="background-color: #16a34a; color: #fff; padding: 10px 18px; text-decoration: none; border-radius: 6px; display: inline-block; font-size: 14px;">Open the admin panel</a>
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
