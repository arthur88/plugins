<?php session_start(); ?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8" />
		<title> Project title |  </title>
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
	   <meta name="viewport" content="width=device-width, initial-scale=1.0">
	</head>
<body>
<div class="container">
<div class="row">
<div class="col-xs-12col-sm-12 col-md-12 col-lg-12">
<?php
    require_once 'WebToPay.php';
    require_once("../inc/class.pay.php");

    $pay = new pay;

if ($_SERVER['REQUEST_METHOD'] === "GET" && isset($_GET['data']) && isset($_GET['ss1']))
{
    $params = array();
    parse_str(base64_decode(strtr($_GET['data'], array('-' => '+', '_' => '/'))), $params);
	$personcode = $params['personcode'];

    //echo values
    var_dump($personcode);

    if($getQTY){
        echo $msg = safe($lg['succPay']);
        echo '<a href="../" class="btn btn-success"> Grįžti</a>';

    } else {
     $msg = safe($lg['error_paying']);
     die();
 }

} else {
    echo $msg = safe($lg['error_paying']);
    echo '<a href="../" class="btn btn-success"> Grįžti</a>';
    die();
}
?>
</div>
</div>
</div>
</body>
</html>

