<?php
class Settings_Payment {
	// EPAY //

 	public static $epay_url = 'https://www.epay.bg/';
	public static $easypay_url = 'https://www.epay.bg/ezp/reg_bill.cgi';
	public static $epay_min = 'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx';
	public static $epay_secret = 'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx';
    
	// public static $epay_url = 'https://demo.epay.bg/';
	// public static $epay_min = 'xxxxxxxxxxxxxxxxx';
	// public static $epay_secret = 'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx';

    
    
	// PAYPAL //
	public static $paypal_url = 'https://www.paypal.com/cgi-bin/webscr';
	public static $paypal_receiver_email = 'pay@novahost.bg';
	public static $paypal_receiver_id = 'xxxxxxxxxxxxxxxxxxxxxxxxx';
	public static $paypal_api_url = 'https://api-3t.paypal.com/nvp';
	public static $paypal_api_username = 'pay_api1.novahost.bg';
	public static $paypal_api_password = 'xxxxxxxxxxxxxxxxxxxxxxxx';
	public static $paypal_api_signature = 'xxxxxxxxxx.xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx';
 
/*	
	public static $paypal_url = 'https://www.sandbox.paypal.com/cgi-bin/webscr';
	public static $paypal_receiver_email = 'seller_1334934255_biz@smshosting.bg';
	public static $paypal_receiver_id = 'xxxxxxxxxxxxx';
	public static $paypal_api_url = 'https://api-3t.sandbox.paypal.com/nvp';
	public static $paypal_api_username = 'seller_1334934255_biz_api1.smshosting.bg';
	public static $paypal_api_password = 'xxxxxxxxxxxxxxxxxx';
	public static $paypal_api_signature = 'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx';
 */   
    
    
    // ORDERS //
    public static $order_expire_time = 3600;
    public static $order_expire_time_easypay = 604800;
    
    
    // CURRENCY //
    public static $currency_BGN_to_EUR = .5112918811962185;
}
?>
