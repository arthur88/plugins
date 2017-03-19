<?php
	require_once("inc/class.pay.php");
	require_once("libwebtopay/WebToPay.php");

	$pay = new pay;

if ($_SERVER['REQUEST_METHOD'] === "GET" && isset($_GET['data']) && isset($_GET['ss1'])) {
    $params = array();
    parse_str(base64_decode(strtr($_GET['data'], array('-' => '+', '_' => '/'))), $params);
    $values = explode(" ", $params['sms']);

    $getQTY = $pay->qty_by_code(safe($values['0']));

    if($getQTY == TRUE){	echo 'OK Ačiū, kad siuntėte';	} else {}
} else { die(); }
?>
