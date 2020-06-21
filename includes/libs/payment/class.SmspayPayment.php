<?php
class SmspayPayment extends SmsPayment {
	public function process() {
		if (!isset($_POST['id'], $_POST['sid'], $_POST['vasms'], $_POST['vanumber'], $_POST['text'], $_POST['msisdn'])) {
			return;
		}
		
        $response = '+OK ' . $this->validateSms(array(
            'text' => $_POST['text'],
            'price' => $_POST['vasms'],
        ));
        
		echo $response;
		
		file_put_contents('smspay.txt', var_export($_POST, true) . PHP_EOL . $response . PHP_EOL . str_repeat('=', 80) . PHP_EOL, FILE_APPEND);
	}
}
?>
