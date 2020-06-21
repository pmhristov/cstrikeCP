<?php
class PaypalPayment extends Payment {
	public static function request($button_id) {
        return array(
            'cmd' => '_s-xclick',
            'hosted_button_id' => $button_id,
        );
	}
	

	public function process() {
		global $DB, $DB_PAYMENT;
		if ('POST' !== $_SERVER['REQUEST_METHOD']) {
			return;
		}
		$query = http_build_query(array_merge($_POST, array('cmd' => '_notify-validate')));
		//$response = file_get_contents(Settings_Payment::$paypal_url . '?' . $query);
		$ch = curl_init();
		curl_setopt_array($ch, array(
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_URL => Settings_Payment::$paypal_url . '?' . $query,
			CURLOPT_USERAGENT => 'Mozilla/5.0',
		));
		$response = curl_exec($ch);
		curl_close($ch);
		
		file_put_contents('/var/www/vhosts/novahost.bg/logs/callback_smshosting.txt', 'PayPal:' . var_export($_POST, true) . PHP_EOL . var_export($query, true) . PHP_EOL . var_export($response, true) . PHP_EOL, FILE_APPEND);

		if ('VERIFIED' === $response) {
			file_put_contents('/var/www/vhosts/novahost.bg/logs/callback_smshosting.txt', 'OPSA', FILE_APPEND);
			if (
				isset(
					$_POST['payment_status'],
					$_POST['receiver_email'],
					$_POST['receiver_id'],
					$_POST['txn_type']
				) &&
				Settings_Payment::$paypal_receiver_email === $_POST['receiver_email'] &&
				Settings_Payment::$paypal_receiver_id === $_POST['receiver_id'] &&
				'web_accept' === $_POST['txn_type']
			) {

                $order = $DB_PAYMENT->GetRow('SELECT * FROM `cstrikecp` WHERE `id` = ?', array($_POST['item_number']));
                
				if ($order['Status'] !== 'PAID') {
					$ppbm = new PayPalButtonManager;
					$ppbm->delete($order['paypal_button_id']);
					
					switch ($_POST['payment_status']) {
						case 'Completed':
							addBalance($order['service_id'], $order['amount'], $order['type']);					
							$order_update = array(
								'Status' => 'PAID',
							);

							$DB_PAYMENT->AutoExecute('cstrikecp', $order_update, 'UPDATE', 'id = ' . $order['id']);
					   break;
						case 'Denied':
						$DB_PAYMENT->Execute('DELETE FROM `cstrikecp` WHERE `id` = ?', array($order['id']));
						//case 'Expired':
						case 'Failed':
						$DB_PAYMENT->Execute('DELETE FROM `cstrikecp` WHERE `id` = ?', array($order['id']));
						case 'Refunded':
						$DB_PAYMENT->Execute('DELETE FROM `cstrikecp` WHERE `id` = ?', array($order['id']));
						case 'Reversed':
						$DB_PAYMENT->Execute('DELETE FROM `cstrikecp` WHERE `id` = ?', array($order['id']));
						//case 'Voided':
							// delete from orders where id = $order['id']
							//$DB->Execute('DELETE FROM `order` WHERE `id` = ?', array($order['id']));
						break;
					}
				}
			}

			file_put_contents('/var/www/vhosts/novahost.bg/logs/callback_smshosting.txt', 'PayPal:' . var_export($_POST, true) . PHP_EOL . var_export($order, true) . PHP_EOL . $query . PHP_EOL . $response . PHP_EOL . str_repeat('=', 80) . PHP_EOL, FILE_APPEND);
		}
	}
}
?>
