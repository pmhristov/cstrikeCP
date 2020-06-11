<?php
session_start();
require_once('../../includes/core.php');

$value = trim($_POST['value']);
$payment_method = trim($_POST['payment_method']);
$status = 0;

if (in_array($value, $prices)) {
	$value_replace = str_replace(',', '.', $value);
	$value = $value_replace;
	if ($payment_method == 'sms') {
	$data_sms = <<<DATA
{00000000}
{$value}
DATA;
	$secret = 'xxxxxxxxxxxxxxxxxxxxxx'; //SMS Mobio.BG Secret
	$encoded  = base64_encode($data_sms);
	$checksum = hash_hmac('sha1', $encoded, $secret);	

					$status = 2;
					$msg = '<form method="post" action="http://sms.novahost.bg">';
					$msg .= '<input name="encoded" type="hidden" value="' . $encoded . '"/>';
					$msg .= '<input name="checksum" type="hidden" value="' . $checksum . '"/>';
					$msg .= '<strong>Цената през СМС е по - висока тъй като мобилния оператор удържа над 50% от цената.</strong><br />';
					$msg .= '<input id="mobio_sms_submit" type="submit" value="Плати" />';
					$msg .= '</form>';
	}

	if ($payment_method == 'paypal') {
		require '../../includes/libs/payment/class.Settings.php';
		require '../../includes/libs/payment/class.PayPalButtonManager.php';
		require '../../includes/libs/payment/class.Payment.php';
		require '../../includes/libs/payment/class.PaypalPayment.php';


		$DB->AutoExecute('orders', array(
			'type' => $payment_method,
			'service' => 'cstrike-balance',
			'plan' => $value,
			'service_id' => $_SESSION['id'],
			'mail' => $_SESSION['mail'],
		), 'INSERT');
		$order_id = $DB->Insert_ID();

		$fields = array(
			'item_name' => 'Зареждане на баланс (cstrike' . $_SESSION['id'] . ')',
			'item_number' => $order_id,
			'quantity' => 1,
			'amount' => $value,
			'url_return_paid' => 'http://cstrikecp.smshosting.bg/',
			'url_return_canceled' => 'http://cstrikecp.smshosting.bg/',
		);

		$ppbm = new PayPalButtonManager;
		$ppbm->create($fields);
		$paypal_button_id = $ppbm->getButtonId();

		$DB->AutoExecute('orders', array(
			'paypal_button_id' => $paypal_button_id,
		), 'UPDATE', '`id` = ' . $DB->qstr($order_id));

		$payment_data = array(
			'paypal' => array(
				'url' => Settings_Payment::$paypal_url,
				'fields' => PaypalPayment::request($paypal_button_id),
			),
		);


		if (isset($paypal_button_id)) {
			$status = 2;
			$msg = '<form method="post" action="' . $payment_data['paypal']['url'] . '">';
			foreach ($payment_data['paypal']['fields'] as $key => $value) {
				$msg .= '<input type="hidden" name="' . $key . '" value="' . $value . '" />';
			}
			$msg .= '</form>';		
		}
	}

	
	if ($payment_method == 'epay') {
		require '../../includes/libs/payment/class.Settings.php';
		require '../../includes/libs/payment/class.Payment.php';
		require '../../includes/libs/payment/class.EpayPayment.php';

		$order = array(
			'type' => $payment_method,
			'service' => 'cstrike-balance',
			'plan' => $value,
			'service_id' => $_SESSION['id'],
			'mail' => $_SESSION['mail'],
		);
		
		$epay_invoice_id = getNextEpayInvoiceId();
		
		if ($epay_invoice_id === false) {
			$msg = 'Грешка при генерирането на фактура.';
		}
		else {
			$order['epay_invoice_id'] = $epay_invoice_id;
			$DB->AutoExecute('orders', $order, 'INSERT');

			$order_id = $DB->Insert_ID();

			$fields = array(
				'page' => 'paylogin',
				'item_name' => 'Зареждане на баланс (cstrike' . $_SESSION['id'] . ')',
				'item_number' => $order_id,
				'quantity' => 1,
				'amount' => $value,
				'url_return_paid' => 'http://www.smshosting.bg/',
				'url_return_canceled' => 'http://www.smshosting.bg/',
			);

			$payment_data = array(
				'epay' => array(
					'url' => Settings_Payment::$epay_url,
					'fields' => EpayPayment::request($fields + array(
						'epay_id' => $order['epay_invoice_id'],
						'expire' => time() + Settings_Payment::$order_expire_time,
					)),
				),
			);

					$status = 2;
					$msg = '<form method="post" action="' . $payment_data['epay']['url'] . '">';
					foreach ($payment_data['epay']['fields'] as $key => $value) {
						$msg .= '<input type="hidden" name="' . $key . '" value="' . $value . '" />';
					}
					$msg .= '</form>';			
		}
	}
	
	if ($payment_method == 'creditcard') {
		require '../../includes/libs/payment/class.Settings.php';
		require '../../includes/libs/payment/class.Payment.php';
		require '../../includes/libs/payment/class.EpayPayment.php';

		$order = array(
			'type' => $payment_method,
			'service' => 'cstrike-balance',
			'plan' => $value,
			'service_id' => $_SESSION['id'],
			'mail' => $_SESSION['mail'],
		);
		
		$epay_invoice_id = getNextEpayInvoiceId();
		
		if ($epay_invoice_id === false) {
			$msg = 'Грешка при генерирането на фактура.';
		}
		else {
			$order['epay_invoice_id'] = $epay_invoice_id;
		$DB->AutoExecute('orders', $order, 'INSERT');

		$order_id = $DB->Insert_ID();

		$fields = array(
			'page' => 'paylogin',
			'item_name' => 'Зареждане на баланс (cstrike' . $_SESSION['id'] . ')',
			'item_number' => $order_id,
			'quantity' => 1,
			'amount' => $value,
			'url_return_paid' => 'http://www.smshosting.bg/',
			'url_return_canceled' => 'http://www.smshosting.bg/',
		);

		$payment_data = array(
			'epay' => array(
				'url' => Settings_Payment::$epay_url,
				'fields' => EpayPayment::request($fields + array(
					'epay_id' => $order['epay_invoice_id'],
					'expire' => time() + Settings_Payment::$order_expire_time,
				)),
			),
		);

					$status = 2;
					$msg = '<form method="post" action="' . $payment_data['epay']['url'] . '">';
					foreach ($payment_data['epay']['fields'] as $key => $value) {
						$msg .= '<input type="hidden" name="' . $key . '" value="' . $value . '" />';
					}
					$msg .= '</form>';			
		}
		

	}
	
	if ($payment_method == 'easypay') {
		require '../../includes/libs/payment/class.Settings.php';
		require '../../includes/libs/payment/class.Payment.php';
		require '../../includes/libs/payment/class.EasyPayPayment.php';

		$order = array(
			'type' => $payment_method,
			'service' => 'cstrike-balance',
			'plan' => $value,
			'service_id' => $_SESSION['id'],
			'mail' => $_SESSION['mail'],
		);
		
		$epay_invoice_id = getNextEpayInvoiceId();
		
		if ($epay_invoice_id === false) {
			$msg = 'Грешка при генерирането на фактура.';
		}
		else {
			$order['epay_invoice_id'] = $epay_invoice_id;
			$DB->AutoExecute('orders', $order, 'INSERT');

			$order_id = $DB->Insert_ID();

			$fields = array(
				'item_name' => 'Зареждане на баланс (cstrike' . $_SESSION['id'] . ')',
				'item_number' => $order_id,
				'quantity' => 1,
				'amount' => $value,
			);

			$payment_data = array(
				'easypay' => array(
					'url' => Settings_Payment::$easypay_url,
					'fields' => EasyPayPayment::request($fields + array(
						'epay_id' => $order['epay_invoice_id'],
						'expire' => time() + Settings_Payment::$order_expire_time_easypay,
					)),
				),
			);
		
			$URL = $payment_data['easypay']['url'];
			
			$ch = curl_init($URL);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $payment_data['easypay']['fields']);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1); 
			
			$output = curl_exec($ch);
			curl_close($ch);
			
			$code = explode('=', $output);
			$code = $code[1];
			
			send_email_payment($_SESSION['mail'], 'EasyPay код за плащане (cstrike' . $_SESSION['id'] . ')', $payment_method, $code, true);
			
			$status = 1;
			$msg = $returnedData_msg['request'];			
		}
		

	}	
	
	
	
	if ($payment_method == 'cash' || $payment_method == 'post') {
		
		$DB->AutoExecute('orders', array(
			'type' => $payment_method,
			'service' => 'cstrike-balance',
			'plan' => $value,
			'service_id' => $_SESSION['id'],
			'mail' => $_SESSION['mail'],
		), 'INSERT');
		
			send_email_payment($_SESSION['mail'], 'Зареждане на баланс (cstrike' . $_SESSION['id'] . ')	', $payment_method, $value, true);

			$status = 1;
			$msg = $returnedData_msg['request'];		
	}
}
else {
	$msg = 'Грешка: невалидна сума';
}





echo json_encode(
	array(
		'status' => $status,
		'msg' => $msg,
	)
);



?>
