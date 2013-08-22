<?php

require_once 'CoinJar.php';

$coinjar = new CoinJar('YOUR CHECKOUT USER', 'YOUR CHECKOUT PASSWORD', 'YOUR API KEY', true);
$account = $coinjar->accountInformation();
print_r($account);

$items[0]['name'] = 'Name';
$items[0]['quantity'] = 1;
$items[0]['amount'] = 2;
$order = $coinjar->createOrder($items, 'usd', 'invoice#1', 'coinjar-php', 'notify-url', 'retrn-url', 'cancel-url');
print_r($order);

?>

?>
