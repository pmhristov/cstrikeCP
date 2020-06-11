<?php
if (!in_array('sha1', hash_algos())) {
	throw new Exception('Hash library does not support SHA1!');
}

class EpayPayment extends Payment {
	public static function request($data) {
		$response = array(
			'PAGE' => $data['page'],
			'ENCODED' => base64_encode(implode("\n", array(
				'MIN=' . Settings_Payment::$epay_min,
				'INVOICE=' . $data['epay_id'],
				'AMOUNT=' . number_format($data['quantity'] * $data['amount'], 2, '.', ''),
				'CURRENCY=BGN',
				'EXP_TIME=' . date('d.m.Y H:i:s', $data['expire']),
				'DESCR=' . $data['item_name'] . ' (' . $data['item_number'] . ')',
				'ENCODING=utf-8',
			))),
			'URL_OK' => $data['url_return_paid'],
			'URL_CANCEL' => $data['url_return_canceled'],
		);

		$response['CHECKSUM'] = hash_hmac('sha1', $response['ENCODED'], Settings_Payment::$epay_secret);
		
		return $response;
	}
	
	public function process() {
		global $DB;
		
		if (!isset($_POST['encoded'], $_POST['checksum'])) {
			echo 'ERR=Bad request', "\n";
			
			return;
		}

		if ($_POST['checksum'] !== hash_hmac('sha1', $_POST['encoded'], Settings_Payment::$epay_secret)) {
			echo 'ERR=Invalid checksum', "\n";
			
			return;
		}
		
		$response = '';
		
        $decoded = base64_decode($_POST['encoded']);
		$lines = explode("\n", $decoded);
		
		foreach ($lines as $line) {
			if (preg_match('/^INVOICE=(?P<invoice>\d+):STATUS=(?P<status>PAID|DENIED|EXPIRED)(?P<extra>:PAY_TIME=(?P<pay_time>\d+):STAN=(?P<stan>\d+):BCODE=(?P<bcode>[0-9a-zA-Z]+))?$/', $line, $details)) {
                $order = $DB->GetRow('SELECT * FROM `cstrikecp` WHERE `epay_invoice_id` = ?', array($details['invoice']));
				
				if (empty($order)) {
					$response .= 'INVOICE=' . $details['invoice'] . ':STATUS=NO' . "\n";
					
					continue;
				}
				
				try {
					switch ($details['status']) {
						case 'PAID':
							$DB_PAYMENT->AutoExecute('cstrikecp', array('paid' => 1), 'UPDATE', '`id` = ' . $DB->qstr($order['id']));
						break;
						case 'DENIED':
						case 'EXPIRED':
							$DB_PAYMENT->Execute('DELETE FROM `cstrikecp` WHERE `id` = ?', array($order['id']));
						break;
					}
					
					$response .= 'INVOICE=' . $details['invoice'] . ':STATUS=OK' . "\n";
				}
				catch (Exception $e) {
					$response .= 'INVOICE=' . $details['invoice'] . ':STATUS=ERR' . "\n";
				}
				
			}
		}
        
		echo $response;
		
		file_put_contents('/var/www/vhosts/www.smshosting.bg/epay.txt', $decoded . PHP_EOL . $response . str_repeat('=', 80) . PHP_EOL, FILE_APPEND);
	}
}
?>
