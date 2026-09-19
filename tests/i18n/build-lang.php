<?php
/**
 * Writes tests/i18n/translations.php into lang/{en,pt_BR,es,fr,de}.json and checks
 * that every key used by the player site has a translation in every language.
 *
 *   php tests/i18n/build-lang.php          write + check
 *   php tests/i18n/build-lang.php --check  check only
 */
$base    = dirname(__DIR__, 2);
$table   = require __DIR__ . '/translations.php';
$langs   = ['en', 'pt_BR', 'es', 'fr', 'de'];
$checkOnly = in_array('--check', $argv, true);

// keys used in the code
$used = [];
$collect = function (string $file, string $regex) use (&$used) {
    if (preg_match_all($regex, file_get_contents($file), $m)) {
        foreach ($m[2] as $k) {
            $k = stripcslashes($k);
            if ($k !== '' && !str_contains($k, '${')) $used[$k][] = $file;
        }
    }
};
$js  = '/(?:\$t|(?<![\w.])trans)\(\s*([\'"])((?:\\\\.|(?!\1).)*)\1/s';
$php = '/(?<![\w>])(?:__|trans)\(\s*([\'"])((?:\\\\.|(?!\1).)*)\1/s';

$it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator("$base/resources/js"));
foreach ($it as $f) {
    $p = str_replace('\\', '/', (string) $f);
    if (preg_match('/\.(vue|js)$/', $p) && !str_contains($p, '/Sport/')) $collect($p, $js);
}
foreach (['app/Http/Controllers/Api', 'app/Services'] as $dir) {
    $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator("$base/$dir"));
    foreach ($it as $f) {
        if (str_ends_with((string) $f, '.php')) $collect((string) $f, $php);
    }
}
// keys only reached through variables ($t(category.name), $t(statusLabel), days of the week)
foreach (['All', 'Slots', 'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday',
          'Waiting for payment', 'Confirming on the network', 'Payment confirmed', 'Partially paid', 'Expired', 'Failed'] as $k) {
    $used[$k][] = 'dynamic';
}

$missingInTable = array_diff_key($used, $table);
if ($missingInTable) {
    echo "Keys used in code but not in translations.php:\n";
    foreach ($missingInTable as $k => $files) echo "  - $k   (" . basename($files[0]) . ")\n";
}

$problems = count($missingInTable);
foreach ($langs as $i => $lang) {
    $file = "$base/lang/$lang.json";
    $data = is_file($file) ? json_decode(file_get_contents($file), true) : [];
    if (!is_array($data)) {
        echo "$lang.json is not valid JSON\n";
        $problems++;
        continue;
    }

    foreach ($table as $key => $values) {
        if (!isset($values[$i]) || $values[$i] === '') {
            echo "translations.php: '$key' has no $lang value\n";
            $problems++;
            continue;
        }
        $data[$key] = $values[$i];
    }

    // placeholders must survive translation
    foreach ($table as $key => $values) {
        preg_match_all('/:[a-z]+/', $key, $a);
        preg_match_all('/:[a-z]+/', $values[$i] ?? '', $b);
        if (array_diff($a[0], $b[0])) {
            echo "$lang: '$key' lost a placeholder\n";
            $problems++;
        }
    }

    if (!$checkOnly) {
        ksort($data, SORT_STRING | SORT_FLAG_CASE);
        file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . "\n");
    }

    $absent = array_diff_key($used, $data);
    printf("%-6s %4d keys, %d used keys missing\n", $lang, count($data), count($absent));
    $problems += count($absent);
}

echo $problems ? "$problems problem(s)\n" : "OK: every used key is translated in " . implode(', ', $langs) . "\n";
exit($problems ? 1 : 0);
