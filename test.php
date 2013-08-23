<?php

require_once 'CoinJar.php';
require_once 'config.php';

$coinjar = new CoinJar($user, $secret, $apikey, true);
$account = $coinjar->accountInformation();
print_r($account);

$items[0]['name'] = 'Name';
$items[0]['quantity'] = 1;
$items[0]['amount'] = 2;
$order = $coinjar->createOrder($items, 'USD', 'invoice#1', 'coinjar-php', 'notify-url', 'retrn-url', 'cancel-url');
print_r($order);


//$ipn = $coinjar->simulateIPN('notify-url', 'uuid', 'amount', 'fee', 'currency', 'bitcoin_amount', 'bitcoin_address', 'merchant_reference', 'merchant_invoice');
//print_r($ipn);




?>
