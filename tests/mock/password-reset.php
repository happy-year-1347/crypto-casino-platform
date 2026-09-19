<?php
/**
 * Password reset flow.
 *
 * Two problems this covers:
 *  - the reset token was not tied to the e-mail address and never expired, so a
 *    token issued to one player could reset another player's password;
 *  - when no mail server is configured the endpoint threw a 500 at the player.
 *
 *   php tests/mock/password-reset.php
 */
require __DIR__ . '/../../vendor/autoload.php';

$app    = require __DIR__ . '/../../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::create('/', 'GET'));

use App\Http\Controllers\Api\Auth\AuthController;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

function player(string $email): User
{
    $user = User::firstOrCreate(
        ['email' => $email],
        ['name' => 'Reset Test', 'password' => Hash::make('secret123'), 'phone' => '0000000000', 'role_id' => 1]
    );
    if (!Wallet::where('user_id', $user->id)->exists()) {
        Wallet::create(['user_id' => $user->id, 'currency' => 'BRL', 'symbol' => 'R$', 'active' => 1]);
    }
    return $user;
}

$victim   = player('reset-victim@example.com');
$attacker = player('reset-attacker@example.com');
$controller = app(AuthController::class);
$failed = 0;

function check(string $name, bool $ok, string $detail = ''): void
{
    global $failed;
    if (!$ok) $failed++;
    printf("%-4s %s%s\n", $ok ? 'OK' : 'FAIL', $name, $ok || $detail === '' ? '' : "  -> $detail");
}

// 1. asking for a link works when mail works
Mail::fake();
$response = $controller->submitForgetPassword(Request::create('/', 'POST', ['email' => $victim->email]));
check('asking for a reset link returns 200', $response->getStatusCode() === 200, 'status ' . $response->getStatusCode());

$row = DB::table('password_reset_tokens')->where('email', $victim->email)->first();
check('a token was stored', $row !== null);
check('the token is long enough to not be guessed', $row && strlen($row->token) >= 32, $row ? strlen($row->token) . ' chars' : '');

// 2. that token must not reset a different account
$attackerToken = $row->token;
$response = $controller->submitResetPassword(Request::create('/', 'POST', [
    'email' => $attacker->email, 'token' => $attackerToken,
    'password' => 'hacked123', 'password_confirmation' => 'hacked123',
]));
$body = json_decode($response->getContent(), true);
check("another player's token cannot reset this account", $response->getStatusCode() === 400, json_encode($body));
check('the attacker password did not change', Hash::check('secret123', $attacker->fresh()->password));

// 3. the rightful owner can use it
$response = $controller->submitResetPassword(Request::create('/', 'POST', [
    'email' => $victim->email, 'token' => $attackerToken,
    'password' => 'newpass123', 'password_confirmation' => 'newpass123',
]));
check('the owner can use their own token', $response->getStatusCode() === 200, $response->getContent());
check('the password really changed', Hash::check('newpass123', $victim->fresh()->password));
check('the token was used up', DB::table('password_reset_tokens')->where('email', $victim->email)->doesntExist());

// 4. an expired token is refused
DB::table('password_reset_tokens')->insert([
    'email' => $victim->email, 'token' => $old = Str::random(40),
    'created_at' => now()->subMinutes(90),
]);
$response = $controller->submitResetPassword(Request::create('/', 'POST', [
    'email' => $victim->email, 'token' => $old,
    'password' => 'later123', 'password_confirmation' => 'later123',
]));
check('an old token is refused', $response->getStatusCode() === 400, $response->getContent());
DB::table('password_reset_tokens')->where('email', $victim->email)->delete();

// 5. a broken mail server gives a message, not a 500
// (needs Mockery, which is a dev dependency, so it is skipped on a live server)
if (!class_exists(\Mockery::class)) {
    echo "SKIP mail failure check (Mockery not installed, production install)\n";
    foreach ([$victim, $attacker] as $u) {
        Wallet::where('user_id', $u->id)->delete();
        DB::table('password_reset_tokens')->where('email', $u->email)->delete();
        $u->forceDelete();
    }
    echo $failed ? "\n$failed check(s) failed\n" : "\nPassword reset is safe\n";
    exit($failed ? 1 : 0);
}

Mail::shouldReceive('send')->andThrow(new RuntimeException('Connection could not be established'));
$response = $controller->submitForgetPassword(Request::create('/', 'POST', ['email' => $victim->email]));
$body = json_decode($response->getContent(), true);
check('mail failure answers 503, not 500', $response->getStatusCode() === 503, 'status ' . $response->getStatusCode());
check('the player gets a readable message', !empty($body['error']), $response->getContent());
check('no unusable token is left behind', DB::table('password_reset_tokens')->where('email', $victim->email)->doesntExist());

// clean up
foreach ([$victim, $attacker] as $u) {
    Wallet::where('user_id', $u->id)->delete();
    DB::table('password_reset_tokens')->where('email', $u->email)->delete();
    $u->forceDelete();
}

echo $failed ? "\n$failed check(s) failed\n" : "\nPassword reset is safe\n";
exit($failed ? 1 : 0);
