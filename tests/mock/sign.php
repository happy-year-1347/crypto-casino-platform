<?php
/**
 * Signs an IPN payload the way NOWPayments does.
 *
 *   echo '{"payment_id":1,"payment_status":"finished"}' | php tests/mock/sign.php MY_IPN_SECRET
 *
 * Prints the key-sorted JSON on line 1 and the HMAC-SHA512 signature on line 2.
 */
$secret = $argv[1] ?? '';
$source = isset($argv[2]) && is_file($argv[2]) ? $argv[2] : 'php://stdin';
$raw    = file_get_contents($source);
$raw    = preg_replace('/^\xEF\xBB\xBF/', '', $raw); // strip a UTF-8 BOM if an editor added one
$data   = json_decode(trim($raw), true);

if (!is_array($data)) {
    fwrite(STDERR, "sign.php: could not parse JSON input\n");
    exit(1);
}

$ksort = function (array $a) use (&$ksort) {
    ksort($a);
    foreach ($a as $k => $v) {
        if (is_array($v)) $a[$k] = $ksort($v);
    }
    return $a;
};

$json = json_encode($ksort($data), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
echo $json, PHP_EOL, hash_hmac('sha512', $json, $secret), PHP_EOL;
