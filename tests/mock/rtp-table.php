<?php
/**
 * What the RTP field in Admin > Games really does for the 12 local slots.
 *
 * A spin picks one result out of a bag holding `rtp` winning rows and
 * `100 - rtp` losing ones, capped by how many rows the game actually has. The
 * prize is cpl * betamount * payout while the stake is cpl * betamount *
 * num_line, so a winning row pays payout / num_line times the bet.
 *
 * Run: php tests/mock/rtp-table.php
 */

$base = __DIR__ . '/../../app/Http/Controllers/Games/SpinData';
$ctrl = __DIR__ . '/../../app/Http/Controllers/Games';

/** game name => [spin data folder, controller file] */
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

/** what a row pays: index 5 when the game fills it, otherwise the win detail */
function rowPayout(array $row): float
{
    if (isset($row[5]) && is_numeric($row[5])) {
        return (float) $row[5];
    }
    $sum = 0.0;
    foreach (($row[2] ?? []) as $line) {
        $sum += (float) ($line['payout'] ?? 0) * max(1, (float) ($line['multiply'] ?? 1));
    }
    return $sum;
}

function numLine(string $file): int
{
    if (!is_file($file)) {
        return 5;
    }
    if (preg_match('/"num_line"\s*=>\s*(\d+)/', file_get_contents($file), $m)) {
        return (int) $m[1];
    }
    return 5;
}

$steps = [1, 2, 5, 10, 20, 30, 50, 90];

printf("How much of every 1 staked the game gives back, for each RTP setting\n");
printf("(0.95 is a normal casino. 1.00 is break even. Above 1.00 the house pays out more than it takes.)\n\n");
printf("%-21s %5s %5s %5s %8s", 'Game', 'line', 'wins', 'lose', 'avg pay');
foreach ($steps as $s) {
    printf(" %7s", $s);
}
echo PHP_EOL;

foreach ($games as $name => [$folder, $controller]) {
    $dir = $base . '/' . $folder;
    $win = is_dir($dir) ? loadPool($dir, 'Win') : null;
    $lose = is_dir($dir) ? loadPool($dir, 'Lose') : null;
    if ($win === null || $lose === null) {
        printf("%-21s  (could not read %s)\n", $name, $folder);
        continue;
    }
    $lines = numLine($ctrl . '/' . $controller . '.php');
    $pays = array_map('rowPayout', $win);
    $avg = $pays ? array_sum($pays) / count($pays) : 0;

    printf("%-21s %5d %5d %5d %8.1f", $name, $lines, count($win), count($lose), $avg);
    foreach ($steps as $rtp) {
        $winLen  = min($rtp, count($win));
        $loseLen = min(100 - $rtp, count($lose));
        $total   = $winLen + $loseLen;
        $ev      = $total > 0 ? ($winLen / $total) * ($avg / $lines) : 0;
        printf(" %7.2f", $ev);
    }
    echo PHP_EOL;
}

echo PHP_EOL;
echo "Rows where the payout has to be read from the win detail are the two games\n";
echo "whose result rows leave index 5 empty; without the fallback they pay nothing.\n";
