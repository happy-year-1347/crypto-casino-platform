<?php
/**
 * Spins each of the 12 local slots a million times through the same selection
 * code the site uses, and reports what the player actually gets back.
 *
 * Run: php tests/mock/rtp-simulate.php [rtp] [spins]
 */

require __DIR__ . '/../../vendor/autoload.php';

$targetRtp = (int) ($argv[1] ?? 92);
$spins     = (int) ($argv[2] ?? 200000);

$base = __DIR__ . '/../../app/Http/Controllers/Games/SpinData';
$ctrl = __DIR__ . '/../../app/Http/Controllers/Games';

$games = [
    'Fortune Tiger'       => ['FortuneTiger', 'FortunetigerController'],
    'Fortune OX'          => ['FortuneOX', 'FortuneoxController'],
    'Fortune Mouse'       => ['FortuneMouse', 'FortunemouseController'],
    'Fortune Panda'       => ['FortunePanda', 'FortunepandaController'],
    'Phoenix Rises'       => ['PhoenixRises', 'PhoenixrisesController'],
    'Queen of Bounty'     => ['QueenOfBounty', 'QueenofbountyController'],
    'Treasures of Aztec'  => ['TreasuresOfAztec', 'TreasuresofaztecController'],
    'Bikini Paradise'     => ['BikiniParadise', 'BikiniparadiseController'],
    'Hood VS Wolf'        => ['HoodVsWoolf', 'HoodvswoolfController'],
    "Jack Frost's Winter" => ['JackFrost', 'JackfrostController'],
    'Song Kran Party'     => ['SongKranParty', 'SongkranpartyController'],
    'Fortune Rabbit'      => ['FortuneRabbit', 'FortunerabbitController'],
];

/** the selection code under test, copied in by including the trait's file */
final class Draw
{
    use \App\Traits\Providers\PrivateGamesTrait;
}

function loadPool(string $dir, string $kind): ?array
{
    foreach (glob($dir . '/*' . $kind . '.php') as $file) {
        $src = file_get_contents($file);
        if (!preg_match('/class\s+(\w+)/', $src, $m) || !preg_match('/namespace\s+([^;]+);/', $src, $ns)) {
            continue;
        }
        $class = trim($ns[1]) . '\\' . $m[1];
        if (!class_exists($class)) {
            eval('?>' . $src);
        }
        $method = 'get' . $kind;
        if (class_exists($class) && method_exists($class, $method)) {
            return $class::$method();
        }
    }
    return null;
}

function numLine(string $file): int
{
    if (is_file($file) && preg_match('/"num_line"\s*=>\s*(\d+)/', file_get_contents($file), $m)) {
        return (int) $m[1];
    }
    return 5;
}

printf("RTP set to %d, %s spins per game, stake of 1 per spin\n\n", $targetRtp, number_format($spins));
printf("%-21s %9s %9s %9s %9s\n", 'Game', 'paid back', 'win rate', 'biggest', 'house keeps');

$allPaid = 0.0;
$count = 0;

foreach ($games as $name => [$folder, $controller]) {
    $dir  = $base . '/' . $folder;
    $win  = is_dir($dir) ? loadPool($dir, 'Win') : null;
    $lose = is_dir($dir) ? loadPool($dir, 'Lose') : null;
    if ($win === null || $lose === null) {
        printf("%-21s  (could not read %s)\n", $name, $folder);
        continue;
    }

    $lines   = numLine($ctrl . '/' . $controller . '.php');
    $paid    = 0.0;
    $wins    = 0;
    $biggest = 0.0;

    for ($i = 0; $i < $spins; $i++) {
        $row = Draw::drawResult($win, $lose, $targetRtp, $lines);
        $pay = Draw::rowPayout($row) / $lines;   // stake is 1
        if ($pay > 0) {
            $wins++;
            $biggest = max($biggest, $pay);
        }
        $paid += $pay;
    }

    $rtp = $paid / $spins;
    printf("%-21s %8.1f%% %8.2f%% %8.0fx %9.1f%%\n", $name, $rtp * 100, $wins / $spins * 100, $biggest, (1 - $rtp) * 100);
    $allPaid += $rtp;
    $count++;
}

if ($count) {
    printf("\naverage across the %d games: %.1f%% paid back\n", $count, $allPaid / $count * 100);
}
