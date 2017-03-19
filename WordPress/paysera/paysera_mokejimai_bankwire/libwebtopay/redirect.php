<?php
    require_once('WebToPay.php');
    require_once("../inc/class.pay.php");

    $pay = new pay;

try {
    $self_url = $pay->get_self_url();

    $request = WebToPay::redirectToPayment(array(
        'projectid'     => 47835,
        'sign_password' => '388e604d61ff6d9f655d4eea2ddcbbc8',
        'orderid'       => 0,
        'amount'        => 1000,
        'currency'      => 'LTL',
        'country'       => 'LT',
        'accepturl'     => $self_url.'/accept.php',
        'cancelurl'     => $self_url.'/cancel.php',
        'callbackurl'   => $self_url.'/callback.php',
        'test'          => 0,
    ));
} catch (WebToPayException $e) {
    // handle exception
}