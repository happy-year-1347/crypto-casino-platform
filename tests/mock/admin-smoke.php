<?php
/**
 * Renders the admin pages touched by the crypto/catalogue work with an
 * authenticated admin, without a browser.
 *
 *   php tests/mock/admin-smoke.php
 */
require __DIR__ . '/../../vendor/autoload.php';

$app    = require __DIR__ . '/../../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

use App\Models\User;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

// boot the app once so facades work
$kernel->handle(Request::create('/', 'GET'));

$admin = User::firstOrCreate(
    ['email' => 'admin-smoke@example.com'],
    ['name' => 'Smoke Admin', 'password' => Hash::make('smoke123456'), 'phone' => '0000000000', 'role_id' => 0]
);
Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
if (!$admin->hasRole('admin')) $admin->assignRole('admin');
if (!Wallet::where('user_id', $admin->id)->exists()) {
    Wallet::create(['user_id' => $admin->id, 'currency' => 'BRL', 'symbol' => 'R$', 'active' => 1]);
}

$pages = [
    '/admin',
    '/admin/crypto-payment',
    '/admin/todos-saques',
    '/admin/todos-depositos',
    '/admin/games',
    '/admin/providers',
    '/admin/categories',
    '/admin/users',
];

$failed = 0;
foreach ($pages as $uri) {
    Auth::guard('web')->login($admin);
    $request  = Request::create($uri, 'GET');
    $request->setLaravelSession($app['session']->driver());
    $response = $kernel->handle($request);
    $status   = $response->getStatusCode();
    $body     = $response->getContent();

    $ok = $status === 200 && !str_contains($body, 'Whoops') && !str_contains($body, 'ErrorException');
    if (!$ok) {
        $failed++;
        $snippet = strip_tags(substr($body, 0, 400));
        echo "FAIL {$uri} -> {$status} {$snippet}\n";
    } else {
        echo "OK   {$uri} -> {$status} (" . strlen($body) . " bytes)\n";
    }
    $kernel->terminate($request, $response);
}

exit($failed ? 1 : 0);
