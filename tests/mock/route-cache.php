<?php
/**
 * `php artisan route:cache` must keep every route.
 *
 * The route files used include_once, so when the cache was built the routes were
 * loaded a second time in the same process and every included group was skipped:
 * the site kept only the admin routes and the whole player site returned 404.
 *
 *   php tests/mock/route-cache.php
 */
$base   = dirname(__DIR__, 2);
$php    = PHP_BINARY;
$artisan = escapeshellarg($base . '/artisan');

function routeCount(string $php, string $artisan): int
{
    exec("\"$php\" $artisan route:list --json 2>&1", $out, $code);
    $json = json_decode(implode('', $out), true);
    return is_array($json) ? count($json) : -1;
}

exec("\"$php\" $artisan route:clear 2>&1");
$plain = routeCount($php, $artisan);

exec("\"$php\" $artisan route:cache 2>&1", $cacheOut, $cacheCode);
$cached = routeCount($php, $artisan);
exec("\"$php\" $artisan route:clear 2>&1");

$failed = 0;
printf("%-4s route:cache builds\n", $cacheCode === 0 ? 'OK' : 'FAIL');
if ($cacheCode !== 0) { $failed++; echo implode("\n", $cacheOut), "\n"; }

printf("%-4s routes without cache: %d\n", $plain > 100 ? 'OK' : 'FAIL', $plain);
if ($plain <= 100) $failed++;

printf("%-4s routes with cache:    %d\n", $cached === $plain ? 'OK' : 'FAIL', $cached);
if ($cached !== $plain) {
    $failed++;
    echo "     caching lost " . ($plain - $cached) . " routes (route files must use require, not include_once)\n";
}

echo $failed ? "\n$failed check(s) failed\n" : "\nRoute cache keeps every route\n";
exit($failed ? 1 : 0);
