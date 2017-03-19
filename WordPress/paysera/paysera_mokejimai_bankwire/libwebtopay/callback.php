<?php
require_once('WebToPay.php');
require_once("../inc/class.pay.php");

$pay = new pay;

try {
    $response = WebToPay::checkResponse($_GET, array(
        'projectid' => 12345,
        'sign_password' => 'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx',
        ));

    if ($response['test'] !== '0') {
        throw new Exception('Testuojama, realus apmokėjimas nebuvo padarytas');
    }
    if ($response['type'] !== 'macro') {
        throw new Exception('tik makro mokėjimai priimami');
    }

    $orderId = $response['orderid'];
    $amount = $response['amount'];
    $currency = $response['currency'];
    //@todo: patikrinti, ar užsakymas su $orderId dar nepatvirtintas (callback gali būti pakartotas kelis kartus)
    //@todo: patikrinti, ar užsakymo suma ir valiuta atitinka $amount ir $currency
    //@todo: patvirtinti užsakymą
    echo 'OK Ačiū, Jūsų žinutė buvo sėkmingai išsiųsta';
}
catch (Exception $e) {
    echo get_class($e).': '.$e->getMessage();
}
