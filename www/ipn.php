<?php
require '../includes/core.php';
require '../includes/libs/payment/class.Settings.php';
require '../includes/libs/payment/class.PayPalButtonManager.php';
require '../includes/libs/payment/class.Payment.php';
require '../includes/libs/payment/class.PaypalPayment.php';

$payment = new PaypalPayment;
$payment->process();

//file_put_contents('/var/www/vhosts/smshosting.bg/paypal.txt', var_export($_POST, true), FILE_APPEND);
//file_put_contents('/var/www/vhosts/smshosting.bg/paypal.txt', var_export($payment, true), FILE_APPEND);
//file_put_contents('/var/www/vhosts/novahost.bg/logs/callback_smshosting.txt', var_export($_POST, true) . ' - ' . var_export($payment, true) . PHP_EOL, FILE_APPEND);


?>
