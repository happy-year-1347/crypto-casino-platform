<?php
/**
 * Game session tokens: signed, tied to the player, and they expire.
 *
 * The game opens in an iframe with the token in the URL, so the token can end up
 * in browser history or a screenshot. It must not work forever, and it must not
 * be forgeable.
 *
 *   php tests/mock/game-token.php
 */
require __DIR__ . '/../../vendor/autoload.php';

$app    = require __DIR__ . '/../../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::create('/', 'GET'));

use App\Helpers\Core as Helper;

$failed = 0;
function check(string $name, bool $ok, string $detail = ''): void
{
    global $failed;
    if (!$ok) $failed++;
    printf("%-4s %s%s\n", $ok ? 'OK' : 'FAIL', $name, $ok || $detail === '' ? '' : "  -> $detail");
}

$token = Helper::MakeToken(['id' => 7, 'game' => 'fortunetiger']);
$data  = Helper::DecToken($token);

check('a fresh token decodes', is_array($data) && ($data['id'] ?? null) == 7, json_encode($data));
check('it carries an expiry', is_array($data) && !empty($data['exp']), json_encode($data));
check('the expiry is about a day out', is_array($data)
    && (int) $data['exp'] > time() + 23 * 3600
    && (int) $data['exp'] <= time() + 25 * 3600, (string) ($data['exp'] ?? ''));

// changing anything breaks the signature
$tampered = substr($token, 0, 5) . 'X' . substr($token, 6);
$result = Helper::DecToken($tampered);
check('a tampered token is refused', is_array($result) && ($result['status'] ?? true) === false, json_encode($result));

// signature removed
$result = Helper::DecToken(explode('.', $token)[0]);
check('a token without its signature is refused', is_array($result) && ($result['status'] ?? true) === false, json_encode($result));

// a token that has run out
$old = Helper::MakeToken(['id' => 7, 'game' => 'fortunetiger', 'exp' => time() - 60]);
$result = Helper::DecToken($old);
check('an expired token is refused', is_array($result) && ($result['status'] ?? true) === false, json_encode($result));

// one that is still inside its window
$soon = Helper::MakeToken(['id' => 7, 'game' => 'fortunetiger', 'exp' => time() + 120]);
$result = Helper::DecToken($soon);
check('a token still inside its window works', is_array($result) && ($result['id'] ?? null) == 7, json_encode($result));

echo $failed ? "\n$failed check(s) failed\n" : "\nGame tokens are signed and expire\n";
exit($failed ? 1 : 0);
