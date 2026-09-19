<?php
/**
 * One-off helper used during the crypto cashier work: appends the new UI
 * strings to lang/pt_BR.json (and leaves en/es/fr on the English fallback).
 *
 *   php tests/mock/add-translations.php
 */
$file = __DIR__ . '/../../lang/pt_BR.json';
$new  = [
    'Cryptocurrency'                            => 'Criptomoeda',
    'No deposit method is available at the moment' => 'Nenhum método de depósito disponível no momento',
    'Enter amount'                              => 'Digite o valor',
    'Min'                                       => 'Mín',
    'Max'                                       => 'Máx',
    'Getting live rate'                         => 'Buscando cotação',
    'You will send approximately'               => 'Você enviará aproximadamente',
    'Provider minimum for this coin'            => 'Mínimo do provedor para esta moeda',
    'Enter an amount to see the live rate'      => 'Digite um valor para ver a cotação',
    'Back'                                      => 'Voltar',
    'Memo / Tag'                                => 'Memo / Tag',
    'Address valid for'                         => 'Endereço válido por',
    'Send the exact amount in one transaction. The balance is credited automatically after network confirmation.' => 'Envie o valor exato em uma única transação. O saldo é creditado automaticamente após a confirmação na rede.',
    'New deposit'                               => 'Novo depósito',
    'Waiting for payment'                       => 'Aguardando pagamento',
    'Confirming on the network'                 => 'Confirmando na rede',
    'Payment confirmed'                         => 'Pagamento confirmado',
    'Partially paid'                            => 'Pago parcialmente',
    'Expired'                                   => 'Expirado',
    'Failed'                                    => 'Falhou',
    'Could not get a rate right now'            => 'Não foi possível obter a cotação agora',
    'Amount is below the provider minimum for this coin' => 'O valor está abaixo do mínimo do provedor para esta moeda',
    'Withdraw'                                  => 'Saque',
    'Withdrawals are not available at the moment. Please contact support.' => 'Saques indisponíveis no momento. Fale com o suporte.',
    'Make sure the address belongs to the selected coin and network' => 'Confira se o endereço pertence à moeda e rede selecionadas',
    'Balance'                                   => 'Saldo',
    'You will receive approximately'            => 'Você receberá aproximadamente',
    'rate is fixed when the payout is sent'     => 'a cotação é fixada no envio do pagamento',
    'You need to accept the terms'              => 'Você precisa aceitar os termos',
    'Withdrawal requested. It will be sent after review.' => 'Saque solicitado. Será enviado após a análise.',
    'Game name'                                 => 'Nome do jogo',
    'Payment confirmed!'                        => 'Pagamento confirmado!',
    'Payment failed or expired'                 => 'Pagamento falhou ou expirou',
    'Waiting for payment...'                    => 'Aguardando pagamento...',
];

$json = file_get_contents($file);
$existing = json_decode($json, true) ?: [];

$lines = [];
foreach ($new as $key => $value) {
    if (array_key_exists($key, $existing)) continue;
    $lines[] = '    ' . json_encode($key, JSON_UNESCAPED_UNICODE) . ': ' . json_encode($value, JSON_UNESCAPED_UNICODE);
}

if (empty($lines)) {
    echo "nothing to add\n";
    exit;
}

$json = rtrim($json);
$json = rtrim(substr($json, 0, strrpos($json, '}')));
$json = rtrim($json, ",\r\n") . ",\n" . implode(",\n", $lines) . "\n}\n";

file_put_contents($file, $json);
echo count($lines) . " strings added to pt_BR.json\n";

// make sure the file is still valid JSON
json_decode(file_get_contents($file), true, 512, JSON_THROW_ON_ERROR);
echo "pt_BR.json is valid\n";
