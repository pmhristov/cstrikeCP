<?php
class PayPalButtonManager {
    const VERSION = '51.0';
    const UTC_FORMAT = 'Y-m-d\TH:i:s\Z';
    
    private $response;
    
    private static function generateButtonVars($data) {
        $result = array();
        $i = 1;
        
        foreach ($data as $val) {
            $result['L_BUTTONVAR' . $i++] = $val;
        }
        
        return $result;
    }
    
    private function makeRequest($data) {
        $response = file_get_contents(Settings_Payment::$paypal_api_url, false, stream_context_create(array(
			'http' => array(
				'method' => 'POST',
				'header' => 'Content-type: application/x-www-form-urlencoded' . "\r\n",
				'content' => http_build_query(array(
                    'USER' => Settings_Payment::$paypal_api_username,
                    'PWD' => Settings_Payment::$paypal_api_password,
                    'SIGNATURE' => Settings_Payment::$paypal_api_signature,
                    'VERSION' => self::VERSION,
                ) + $data),
				'timeout' => 30,
			),
		)));
		
        if (false === $response) {
            throw new Exception('HTTP request failed!');
        }
		
		parse_str($response, $this->response);
        
        if ($this->response['VERSION'] !== self::VERSION) {
            throw new Exception('Incorrect API version');
        }
        
        if (0 !== strpos($this->response['ACK'], 'Success')) {
            throw new Exception('Error');
        }
    }
    
    public function create($data) {
		$this->makeRequest(array(
            'METHOD' => 'BMCreateButton',
            'BUTTONCODE' => 'HOSTED',
            'BUTTONTYPE' => 'BUYNOW',
        ) + self::generateButtonVars(array(
            'item_name=' . $data['item_name'],
            'item_number=' . $data['item_number'],
            'quantity=' . $data['quantity'],
            'amount=' . number_format($data['amount'] * Settings_Payment::$currency_BGN_to_EUR, 2, '.', ''),
            'currency_code=EUR',
            'no_note=1',
            'no_shipping=1',
            'return=' . $data['url_return_paid'],
            'cancel_return=' . $data['url_return_canceled'],
			'notify_url=http://cstrikecp.smshosting.bg/ipn.php',
        )));
    }
    
    public function search($start, $end = null) {
        $params = array(
            'METHOD' => 'BMButtonSearch',
            'STARTDATE' => gmdate(self::UTC_FORMAT, $start),
        );
        
        if (!is_null($end)) {
            $params['ENDDATE'] = gmdate(self::UTC_FORMAT, $end);
        }
        
		$this->makeRequest($params);
        
        $buttons = array();
        
        foreach ($this->response as $key => $value) {
            if (
                !preg_match('/^L_([A-Z]*)([0-9]*)$/', $key, $matches) ||
                !in_array($matches[1], array(
                    'HOSTEDBUTTONID',
                    'BUTTONTYPE',
                    'ITEMNAME',
                    'MODIFYDATE',
                ))
            ) {
                continue;
            }
            
            if (!isset($buttons[$matches[2]])) {
                $buttons[$matches[2]] = array();
            }
            
            $buttons[$matches[2]][$matches[1]] = $value;
        }
        
        ksort($buttons);
        
        return $buttons;
    }
    
    private function manageStatus($id, $status) {
        try {
            $this->makeRequest(array(
                'METHOD' => 'BMManageButtonStatus',
                'HOSTEDBUTTONID' => $id,
                'BUTTONSTATUS' => $status,
            ));
        }
        catch (Exception $e) {
            // who cares?
        }
    }
    
    public function delete($id) {
        $this->manageStatus($id, 'DELETE');
    }
    
    public function getButtonCode() {
        return $this->response['WEBSITECODE'];
    }
    
    public function getButtonLink() {
        return $this->response['EMAILLINK'];
    }
    
    public function getButtonId() {
        return $this->response['HOSTEDBUTTONID'];
    }
    
    public function __construct() {
        $this->response = array();
    }
}
?>
