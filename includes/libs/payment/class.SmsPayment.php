<?php
abstract class SmsPayment extends Payment {
    private static $puzzle_prices = array(
        '1' => '1',
        '2.00' => '2',
        '4.00' => '3',
    );
    
    protected function validateSms($data) {
        $response = 'LuckyPuzzle: ';
        
        if (!array_key_exists('text', $data) || !array_key_exists('price', $data)) {
            $response .= 'Greshna zaqvka, molq svyrjete se s nas za poveche informaciq.';
            
            return $response;
        }
        
        if (!preg_match('/(\d+)/', $data['text'], $regs) || !isset(self::$puzzle_prices[$data['price']])) {
            $response .= 'Greshen tekst, molq sledvaite tochno instrukciite.';
            
            return $response;
        }
        
        $order = $this->database->getOrderSms(
            LayerImageGenerator::tileToRowColumn($regs[1]) +
            array(
                'puzzle_id' => self::$puzzle_prices[$data['price']],
            )
        );
        
        if (!$order) {
            $response .= 'Porachkata ne e namerena, molq izpratete pravilen tekst.';
            
            return $response;
        }
        
        --$order['sms_remaining'];
        
        $this->database->updateOrder($order['id'], array(
            'sms_remaining' => $order['sms_remaining'],
        ));
        
        if ($order['sms_remaining'] > 0) {
            $response .= 'Uspeshno zakupihte 1 plochka. Ot porachkata Vi ostavat oshte ' . $order['sms_remaining'] . '.';
        }
        else {
            if ($this->database->setOrderFlag($order['id'], 0, true)) {
                $ppbm = new PayPalButtonManager;
                $ppbm->delete($order['paypal_button_id']);
                
                $this->website->updateImages(1 << 0 | 1 << 1);
            }
            
            $response .= 'Blagodarim Vi. Uspeshno zaplatihte porachkata si. Jelaem Vi kasmet.';
        }
        
        return $response;
    }
    
	public static function request($data) {
        switch ($data['puzzle_id']) {
            case '1':
                $result = array(
                    'prefix' => 'LUCKY',
                    'number' => '1910',
                );
            break;
            case '2':
                $result = array(
                    'prefix' => 'LUCK',
                    'number' => '1935',
                );
            break;
            case '3':
                $result = array(
                    'prefix' => 'LUCK',
                    'number' => '1978',
                );
            break;
            default:
                $result = array(
                    'prefix' => '&nbsp;',
                    'number' => '&nbsp;',
                );
            break;
        }
        
        return $result;
	}
}
?>
