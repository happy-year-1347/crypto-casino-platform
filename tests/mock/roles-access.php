<?php
/**
 * Roles/permissions stay admin-only after the duplicated RoleResource was
 * replaced with policies (the duplicate also broke `php artisan route:cache`).
 *
 *   php tests/mock/roles-access.php
 */
require __DIR__ . '/../../vendor/autoload.php';

$app    = require __DIR__ . '/../../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role as SpatieRole;

$kernel->handle(Request::create('/', 'GET'));

function ensureUser(string $email, string $role): User
{
    $user = User::firstOrCreate(
        ['email' => $email],
        ['name' => 'Access Test', 'password' => Hash::make('smoke123456'), 'phone' => '0000000000', 'role_id' => 0]
    );
    SpatieRole::firstOrCreate(['name' => $role, 'guard_name' => 'web']);
    if (!$user->hasRole($role)) $user->assignRole($role);
    if (!Wallet::where('user_id', $user->id)->exists()) {
        Wallet::create(['user_id' => $user->id, 'currency' => 'BRL', 'symbol' => 'R$', 'active' => 1]);
    }
    return $user;
}

$admin     = ensureUser('admin-smoke@example.com', 'admin');
$affiliate = ensureUser('affiliate-smoke@example.com', 'afiliado');

$failed = 0;
foreach ([Role::class, Permission::class] as $model) {
    foreach (['viewAny', 'create', 'update', 'delete'] as $ability) {
        foreach ([['admin', $admin, true], ['affiliate', $affiliate, false]] as [$who, $user, $expected]) {
            $allowed = Gate::forUser($user)->allows($ability, $model);
            $ok = $allowed === $expected;
            if (!$ok) $failed++;
            printf("%-4s %-10s %-10s %-22s allowed=%s (expected %s)\n", $ok ? 'OK' : 'FAIL', $who, $ability,
                class_basename($model), var_export($allowed, true), var_export($expected, true));
        }
    }
}

// the duplicate class is gone, so the route cache must build
exec('"' . PHP_BINARY . '" ' . escapeshellarg(base_path('artisan')) . ' route:cache 2>&1', $out, $code);
exec('"' . PHP_BINARY . '" ' . escapeshellarg(base_path('artisan')) . ' route:clear 2>&1');
printf("%-4s route:cache builds\n", $code === 0 ? 'OK' : 'FAIL');
if ($code !== 0) { $failed++; echo implode("\n", $out), "\n"; }

echo $failed ? "\n$failed check(s) failed\n" : "\nRoles and permissions are admin-only, route cache builds\n";
exit($failed ? 1 : 0);
