<?php
    require_once("libwebtopay/WebToPay.php");
    require_once("inc/class.pay.php");
    $pay = new pay;
?>
<?php
    if($_SERVER['REQUEST_METHOD'] === "POST" && isset($_POST['pay_by_bank'])){
        $priceID = $_POST['choosePayment'];
        if(is_numeric($priceID)){
           $payAmount = $priceID;
           $payValues = $pay->payValues_byID($payAmount);
        } else {
            $msg = $lg['incChoose'];
            $payAmount = 0;
        }
    }

    try {
    $self_url = $pay->get_self_url();

    $request = WebToPay::buildRequest(array(
            'projectid'     => 12345,
            'sign_password' => 'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx',
            'orderid'       => 0,
            'amount'        => $payValues['price'],
            'currency'      => 'LTL',
            'country'       => 'LT',
            'accepturl'     => website.'libwebtopay/accept.php',
            'cancelurl'     => website.'libwebtopay/cancel.php',
            'callbackurl'   => website.'libwebtopay/callback.php',
            'test'          => 0,
            'personcode' => $usr->CurrUsrID()
        ));
    } catch (WebToPayException $e) { echo $e->getMessage(); }
?>
<div class="row">
<div class="col-xs-12col-sm-12 col-md-12 col-lg-12">
     <div class="msg"><?php print $msg; ?></div>
     <form action="<?php echo WebToPay::PAY_URL; ?>" method="POST" class="form txtCenter">
        <?php foreach ($request as $key => $val): ?>
            <input type="hidden" name="<?php echo $key ?>" value="<?php echo $val; ?>" />
        <?php endforeach; ?>.
        <label class="alert alert-success fs18 mb10 w100p">
            <?php echo $lg['choosen']?>:
            <?php echo $lg['qty'].": ".$payValues['qty']." ".$lg['qtyUnit']; ?> -
            <?php echo $lg['price'].": ".$payValues['price'] / 100." ".$lg['curr']?>
        </label> <br>
        <input type="submit" value="<?php echo $lg['ConfirmPay']; ?>" name="pay_by_bank" class="btn btn-success btn-lg"/>
        <a href="addfund.php" class="btn btn-primary btn-lg"> <?php echo $lg['back']; ?></a>
     </form>
</div>
</div>