<?php
require_once('../../includes/core.php');

function epay_error($msg) {
	file_put_contents('/var/www/vhosts/novahost.bg/www/payment.txt', date('d-m-y G:i') . 'ERR=CSTRIKECP:' . $msg, FILE_APPEND);
	exit;
}


if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
	epay_error('Invalid request method');
}

if (!array_key_exists('encoded', $_POST) || !array_key_exists('checksum', $_POST)) {
	epay_error('Missing parameters');
}

if (strlen($_POST['checksum']) !== 40) {
	epay_error('Invalid checksum length');
}

$secret = 'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx';
//$secret = 'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx'; //sandbox

if (hash_hmac('sha1', $_POST['encoded'], $secret) !== $_POST['checksum']) {
    epay_error('Not valid CHECKSUM');
}

$lines = base64_decode($_POST['encoded'], true);

if ($lines === false) {
	epay_error('Invalid encoded data');
}


$lines_arr = explode("\n", $lines);

foreach ($lines_arr as $line) {
	if (preg_match('/^INVOICE=(?P<invoice>\d+):STATUS=(?P<status>PAID|DENIED|EXPIRED)(?::PAY_TIME=(?P<pay_time>\d+):STAN=(?P<stan>\d+):BCODE=(?P<bcode>[0-9a-zA-Z]+))?$/', $line, $epay_data)) {
	
		if ($epay_data['status'] == 'PAID') {
			$order = $DB_PAYMENT->GetRow('SELECT * FROM `cstrikecp` WHERE `epay_invoice_id` = ?', array($epay_data['invoice']));
			if ($order['Status'] !== 'PAID') {
				
				if (!array_key_exists('epay_invoice_id', $order)) {
					echo 'INVOICE=', $epay_data['invoice'], ':STATUS=NO', "\n";
					epay_error('No epay_invoice_id in order array');
				}
				
				if (empty($order['amount'])) {
					echo 'INVOICE=', $epay_data['invoice'], ':STATUS=NO', "\n";
					epay_error('No amount in order array');
				}
					
				addBalance($order['service_id'], $order['amount'], $order['type']);
				
				$order_update = array(
					'Status' => 'PAID',
				);

				$DB_PAYMENT->AutoExecute('cstrikecp', $order_update, 'UPDATE', 'id = ' . $order['id']);
				file_put_contents('/var/www/vhosts/novahost.bg/www/payment.txt', date('d-m-y G:i') . 'SUCC=CSTRIKECP:' . $lines, FILE_APPEND);				
				
			}
			else {
				epay_error('Order is already PAID: ' . "\n" . $lines);
			}
		}
		else {
			file_put_contents('/var/www/vhosts/novahost.bg/www/payment.txt', date('d-m-y G:i') . 'ERR=CSTRIKECP:' . $epay_data, FILE_APPEND);
			//epay_error('Unsuccessful: ' . $epay_data['status'] . "\n" . $lines);
		}
		echo 'INVOICE=', $epay_data['invoice'], ':STATUS=OK', "\n";
		$DB_PAYMENT->AutoExecute('epay_invoice_ids', array('status' => $epay_data['status']), 'UPDATE', 'epay_id = ' . $epay_data['invoice']);

	}
	else {
		epay_error('Not Valid epay_data:' . $lines);
	}
}
?>
