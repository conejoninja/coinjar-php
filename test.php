<?php

require_once 'CoinJar.php';

$coinjar = new CoinJar('YOUR CHECKOUT USER', 'YOUR CHECKOUT PASSWORD', 'YOUR API KEY', true);
$account = $coinjar->accountInformation();
print_r($account);

?>
