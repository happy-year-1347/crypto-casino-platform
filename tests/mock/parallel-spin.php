<?php
/**
 * Runs ONE spin through the real game endpoint logic, in its own process, so
 * several spins can hit the same wallet at the same instant.
 *
 *   php tests/mock/parallel-spin.php <email> <bet> <startUnixMs>
 */
require __DIR__ . '/../../vendor/autoload.php';

$app    = require __DIR__ . '/../../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::create('/', 'GET'));

use App\Helpers\Core as Helper;
use App\Http\Controllers\Api\Games\GameController;
use App\Models\Game;
use App\Models\User;
use Illuminate\Http\Request;

$email   = $argv[1];
$bet     = (float) $argv[2];
$startAt = isset($argv[3]) ? (float) $argv[3] : 0;

$user = User::where('email', $email)->firstOrFail();
$game = Game::where('game_code', 'fortunetiger')->firstOrFail();
$token = Helper::MakeToken(['id' => $user->id, 'game' => $game->game_code]);

while ($startAt > 0 && microtime(true) * 1000 < $startAt) {
    usleep(200);
}

$request = Request::create("/api/vgames/{$token}/spin", 'POST', [
    'cpl'       => 1,
    'betamount' => $bet / 5,
    'numline'   => 5,
]);

try {
    $response = app(GameController::class)->sourceProvider($request, $token, 'spin');
    echo $response->getStatusCode(), "\n";
} catch (\Throwable $e) {
    echo "500 ", $e->getMessage(), "\n";
}
