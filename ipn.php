<?php

require_once 'CoinJar.php';
require_once 'config.php';

$coinjar = new CoinJar($user, $secret, $apikey, true);

// data from the post (it's supposed to be sent by CoinJar, is sent by POST!)
if(isset($_POST['uuid']) && isset($_POST['amount']) && isset($_POST['currency']) && isset($_POST['status']) && isset($_POST['ipn_digest'])) {
    if($_POST['status']=='COMPLETED') {
        // calculate digest
        $digest = $coinjar->IPNDigest($_POST['uuid'], $_POST['amount'], $_POST['currency'], $_POST['status']);
        if($digest==$_POST['ipn_digest']) {
            $order = json_decode($coinjar->order($_POST['uuid']));
            if($order!=null) {
                if($_POST['amount']==$order->order->amount && $_POST['currency']==$order->order->currency && $_POST['status']==$order->order->status) {
                    echo "THE REQUEST IS OK!";
                } else {
                    echo "WRONG REQUEST! (data does not match)";
                }
            } else {
                echo "WRONG REQUEST! (order does not exists)";
            }
        } else {
            echo "WRONG REQUEST! (IPN digest is not correct)";
        }
    } else {
        echo "WRONG REQUEST! (status is not COMPLETED)";
    }
} else {
    echo "WRONG REQUEST! (missing fields)";
}


?>
