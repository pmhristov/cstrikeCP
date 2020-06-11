<?php
require '../../includes/core.php';

if (isset($_POST)) {
	$payment_method = trim($_POST['payment_method']);
	$serverid = trim(mysql_real_escape_string($_POST['serverid']));
	$value = trim($_POST['value']);
	$value_replace = str_replace(',', '.', $value);
	$value = $value_replace;
	//$DB_PAYMENT->debug=true;
	
	switch ($payment_method) {
		case 'sms':
	$data_sms = <<<DATA
{$serverid}
{$value}
cstrikecp
DATA;
	$secret = 'xxxxxxxxxxxxxxxxxx'; //Mobio.BG Key
	$encoded  = base64_encode($data_sms);
	$checksum = hash_hmac('sha1', $encoded, $secret);		
		//var_dump($_POST);
	?>
<form method="post" action="http://sms.novahost.bg">
<input name="encoded" type="hidden" value="<?php echo $encoded; ?>"/>
<input name="checksum" type="hidden" value="<?php echo $checksum; ?>"/>
<div class="alert alert-error alert-block">

<strong>Цената през СМС е по - висока тъй като мобилния оператор удържа над 50% от цената.</strong>
</div>
<div class="alert alert-info alert-block">Ще бъдете пренасочен към външна страница за завършване на плащането.</div>
<div style="text-align: center;"><input class="navigation_button btn btn-primary" style="width: 300px;" id="payment_submit" type="submit" value="Направи заплащане"/></div>
</form>
				
<?php
		break;
		case 'paypal':
		require '../../includes/libs/payment/class.Settings.php';
		require '../../includes/libs/payment/class.PayPalButtonManager.php';
		require '../../includes/libs/payment/class.Payment.php';
		require '../../includes/libs/payment/class.PaypalPayment.php';
		
		$DB_PAYMENT->AutoExecute('cstrikecp', array(
			'type' => $payment_method,
			'Status' => 'UNPAID',
			'service_id' => $_SESSION['id'],
			'amount' => number_format($value, 2, '.', ' '),
		), 'INSERT');
		$order_id = $DB_PAYMENT->Insert_ID();	
		
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
		//var_dump($paypal_button_id);
 		$DB_PAYMENT->AutoExecute('cstrikecp', array(
			'paypal_button_id' => $paypal_button_id,
		), 'UPDATE', '`id` = ' . $DB_PAYMENT->qstr($order_id));

		$payment_data = array(
			'paypal' => array(
				'url' => Settings_Payment::$paypal_url,
				'fields' => PaypalPayment::request($paypal_button_id),
			),
		);


		if (isset($paypal_button_id)) {
			echo '<div class="alert alert-info alert-block">Ще бъдете пренасочен към външна страница за завършване на плащането.</div>';
			echo '<form method="post" action="' . $payment_data['paypal']['url'] . '">';
			foreach ($payment_data['paypal']['fields'] as $key => $value) {
				echo '<input type="hidden" name="' . $key . '" value="' . $value . '" />';
			}
			echo '<div style="text-align: center;"><input type="image" id="payment_submit" src="https://www.paypalobjects.com/webstatic/en_US/i/buttons/checkout-logo-large.png" alt="Check out with PayPal"/></div>';
			echo '</form>';		
		}
?>
<?php
		break;
		case 'cash':
?>
Направете банков превод със следните данни:<br />
<div class="alert alert-info alert-block">
Към банка: <strong>Първа инвестиционна банка (FiBank)</strong><br />
Получател: <strong>Нова Хост ЕООД</strong><br />
Банкова сметка (IBAN): <strong>BG53FINV91501015703341</strong><br />
BIC: <strong>FINVBGSF</strong><br />
Относно: <strong>Зареждане на баланс (cstrike<?php echo $_SESSION['id']; ?>)</strong>
</div>
*<strong>Тъй като банковите преводи пристигнат при нас по - бавно, можете да ни изпратите снимка на самото платежно от банката за да активираме вашата услуга по - бързо.</strong>*
<?php
		break;
		case 'epay':
			require '../../includes/libs/payment/class.Settings.php';
			require '../../includes/libs/payment/class.Payment.php';
			require '../../includes/libs/payment/class.EpayPayment.php';

			$epay_invoice_id = getNextEpayInvoiceId();

			if ($epay_invoice_id === false) {
				echo 'Грешка при генерирането на фактура.';
			}
			else {
				
				$DB_PAYMENT->AutoExecute('cstrikecp', array(
					'type' => $payment_method,
					'Status' => 'UNPAID',
					'service_id' => $_SESSION['id'],
					'amount' => number_format($value, 2, '.', ' '),
					'epay_invoice_id' => $epay_invoice_id,
				), 'INSERT');
				$order_id = $DB_PAYMENT->Insert_ID();

				
				$epay_invoice = array(
					'epay_id' => $epay_invoice_id,
					'invoice_id' => $order_id,
					'site' => 'cstrikecp',
					
				);
				
				$DB_PAYMENT->AutoExecute('epay_invoice_ids', $epay_invoice, 'INSERT');

				$fields = array(
					'page' => 'paylogin',
					'item_name' => 'Зареждане на баланс (cstrike' . $_SESSION['id'] . ')',
					'item_number' => $order_id,
					'quantity' => 1,
					'amount' => $value,
					'url_return_paid' => 'http://cstrikecp.smshosting.bg/',
					'url_return_canceled' => 'http://cstrikecp.smshosting.bg/',
				);

				$payment_data = array(
					'epay' => array(
						'url' => Settings_Payment::$epay_url,
						'fields' => EpayPayment::request($fields + array(
							'epay_id' => $epay_invoice_id,
							'expire' => time() + Settings_Payment::$order_expire_time,
						)),
					),
				);
						//var_dump($payment_data);
						echo '<div class="alert alert-info alert-block">Ще бъдете пренасочен към външна страница за завършване на плащането.</div>';
						echo '<form method="post" action="' . $payment_data['epay']['url'] . '">';
						foreach ($payment_data['epay']['fields'] as $key => $value) {
							echo '<input type="hidden" name="' . $key . '" value="' . $value . '" />';
						}
			echo '<div style="text-align: center;"><input class="navigation_button btn btn-primary" style="width: 300px;" id="payment_submit" type="submit" value="Направи заплащане"/></div>';
						echo '</form>';
			}
?>
<?php
		break;
		case 'easypay':
			require '../../includes/libs/payment/class.Settings.php';
			require '../../includes/libs/payment/class.Payment.php';
			require '../../includes/libs/payment/class.EpayPayment.php';
			
			$epay_invoice_id = getNextEpayInvoiceId();

			if ($epay_invoice_id === false) {
				echo 'Грешка при генерирането на фактура.';
			}
			else {
				
				$DB_PAYMENT->AutoExecute('cstrikecp', array(
					'type' => $payment_method,
					'Status' => 'UNPAID',
					'service_id' => $_SESSION['id'],
					'amount' => number_format($value, 2, '.', ' '),
					'epay_invoice_id' => $epay_invoice_id,
				), 'INSERT');
				$order_id = $DB_PAYMENT->Insert_ID();

				
				$epay_invoice = array(
					'epay_id' => $epay_invoice_id,
					'invoice_id' => $order_id,
					'site' => 'cstrikecp',
					
				);
				
				$DB_PAYMENT->AutoExecute('epay_invoice_ids', $epay_invoice, 'INSERT');

				$fields = array(
					//'page' => 'paylogin',
					'item_name' => 'Зареждане на баланс (cstrike' . $_SESSION['id'] . ')',
					'item_number' => $order_id,
					'quantity' => 1,
					'amount' => $value,
					//'url_return_paid' => 'http://cstrikecp.smshosting.bg/',
					//'url_return_canceled' => 'http://cstrikecp.smshosting.bg/',
				);

				$payment_data = array(
					'easypay' => array(
						'url' => Settings_Payment::$easypay_url,
						'fields' => EpayPayment::request($fields + array(
							'epay_id' => $epay_invoice_id,
							'expire' => time() + Settings_Payment::$order_expire_time,
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
				
				echo 'Вашият код за EasyPay е: <strong>' . $code . '</strong><br />';
				echo 'Можете да заплатите фактурата във всяка една каса на EasyPay.<br />';
				echo 'Валидността на кода е 7 дни от неговото генериране.';
			}
?>
<?php
		break;
		case 'creditcard':
			require '../../includes/libs/payment/class.Settings.php';
			require '../../includes/libs/payment/class.Payment.php';
			require '../../includes/libs/payment/class.EpayPayment.php';

			$epay_invoice_id = getNextEpayInvoiceId();

			if ($epay_invoice_id === false) {
				echo 'Грешка при генерирането на фактура.';
			}
			else {
				
				$DB_PAYMENT->AutoExecute('cstrikecp', array(
					'type' => $payment_method,
					'Status' => 'UNPAID',
					'service_id' => $_SESSION['id'],
					'amount' => number_format($value, 2, '.', ' '),
					'epay_invoice_id' => $epay_invoice_id,
				), 'INSERT');
				$order_id = $DB_PAYMENT->Insert_ID();

				
				$epay_invoice = array(
					'epay_id' => $epay_invoice_id,
					'invoice_id' => $order_id,
					'site' => 'cstrikecp',
					
				);
				
				$DB_PAYMENT->AutoExecute('epay_invoice_ids', $epay_invoice, 'INSERT');

				$fields = array(
					'page' => 'credit_paydirect',
					'item_name' => 'Зареждане на баланс (cstrike' . $_SESSION['id'] . ')',
					'item_number' => $order_id,
					'quantity' => 1,
					'amount' => $value,
					'url_return_paid' => 'http://cstrikecp.smshosting.bg/',
					'url_return_canceled' => 'http://cstrikecp.smshosting.bg/',
				);

				$payment_data = array(
					'epay' => array(
						'url' => Settings_Payment::$epay_url,
						'fields' => EpayPayment::request($fields + array(
							'epay_id' => $epay_invoice_id,
							'expire' => time() + Settings_Payment::$order_expire_time,
						)),
					),
				);
						//var_dump($payment_data);
						echo '<div class="alert alert-info alert-block">Ще бъдете пренасочен към външна страница за завършване на плащането.</div>';
						echo '<form method="post" action="' . $payment_data['epay']['url'] . '">';
						foreach ($payment_data['epay']['fields'] as $key => $value) {
							echo '<input type="hidden" name="' . $key . '" value="' . $value . '" />';
						}
			echo '<div style="text-align: center;"><input class="navigation_button btn btn-primary" style="width: 300px;" id="payment_submit" type="submit" value="Направи заплащане"/></div>';
						echo '</form>';
			}		
?>
<?php
		break;
		case 'bpay':
			require '../../includes/libs/payment/class.Settings.php';
			require '../../includes/libs/payment/class.Payment.php';
			require '../../includes/libs/payment/class.EpayPayment.php';
			
			$epay_invoice_id = getNextEpayInvoiceId();

			if ($epay_invoice_id === false) {
				echo 'Грешка при генерирането на фактура.';
			}
			else {
				
				$DB_PAYMENT->AutoExecute('cstrikecp', array(
					'type' => $payment_method,
					'Status' => 'UNPAID',
					'service_id' => $_SESSION['id'],
					'amount' => number_format($value, 2, '.', ' '),
					'epay_invoice_id' => $epay_invoice_id,
				), 'INSERT');
				$order_id = $DB_PAYMENT->Insert_ID();

				
				$epay_invoice = array(
					'epay_id' => $epay_invoice_id,
					'invoice_id' => $order_id,
					'site' => 'cstrikecp',
					
				);
				
				$DB_PAYMENT->AutoExecute('epay_invoice_ids', $epay_invoice, 'INSERT');

				$fields = array(
					//'page' => 'paylogin',
					'item_name' => 'Зареждане на баланс (cstrike' . $_SESSION['id'] . ')',
					'item_number' => $order_id,
					'quantity' => 1,
					'amount' => $value,
					//'url_return_paid' => 'http://cstrikecp.smshosting.bg/',
					//'url_return_canceled' => 'http://cstrikecp.smshosting.bg/',
				);

				$payment_data = array(
					'easypay' => array(
						'url' => Settings_Payment::$easypay_url,
						'fields' => EpayPayment::request($fields + array(
							'epay_id' => $epay_invoice_id,
							'expire' => time() + Settings_Payment::$order_expire_time,
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
				
				echo 'Можете да заплатите фактурата на всеки един банкомат който поддържа опцията B-Pay.<br />';
				echo 'Код на търговеца: <strong>60000</strong><br />';
				echo 'Персонален код: <strong>' . $code . '</strong><br />';
				echo 'Валидността на кода е 7 дни от неговото генериране.';
			}		
		break;
		case 'post':
		
?>
Направете пощенски запис в най - близкия пощенски клон със следните данни:<br />

<div class="alert alert-info alert-block">
Получател: <strong>Петър Мирославов Христов</strong><br />
<strong>До поискване</strong><br />
Град: <strong>Троян</strong><br />
Пощенски код: <strong>5600</strong><br />
Относно: <strong>Зареждане на баланс - ctrike<?php echo $_SESSION['id']; ?></strong><br />
</div>


*<strong>Тъй като пощенските записи се усвояват един път в седмицата, моля изпратете ни снимка на бележката от пощата за да можем да активираме вашата услуга по - бързо.</strong>*
<?php
		break;
	}
}
?>
