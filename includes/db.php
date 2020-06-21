<?php
require_once 'vendor/autoload.php';

$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;

if (defined('DB_TYPE') && defined('DB_USER') && defined('DB_PASS') && defined('DB_HOST') && defined('DB_NAME') && defined('DB_OPTIONS')) {
	$DB = adoNewConnection(DB_TYPE);
	$DB->debug = false;
	$DB->connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
	$DB->setCharset('utf8');
	
}

if (!isset($DB) || !$DB || !($rs_charset = $DB->Execute('SHOW VARIABLES LIKE ?', array('character_set_database')))) {
	header('Content-Type: text/plain; charset=utf-8');
	die('Уебсайтът не може да осъществи връзка с базата данни. Моля изчакайте няколко минути, докато отстраняваме проблема.');
}

if (defined('DB_TYPE_PAYMENT') && defined('DB_USER_PAYMENT') && defined('DB_PASS_PAYMENT') && defined('DB_HOST_PAYMENT') && defined('DB_NAME_PAYMENT') && defined('DB_OPTIONS_PAYMENT')) {
	$DB_PAYMENT = @NewADOConnection(DB_TYPE_PAYMENT . '://' . DB_USER_PAYMENT . ':' . DB_PASS_PAYMENT . '@' . DB_HOST_PAYMENT . '/' . DB_NAME_PAYMENT . '?' . DB_OPTIONS_PAYMENT);
	
	$DB_PAYMENT = adoNewConnection(DB_TYPE_PAYMENT);
	$DB_PAYMENT->debug = false;
	$DB_PAYMENT->connect(DB_HOST_PAYMENT, DB_USER_PAYMENT, DB_PASS_PAYMENT, DB_NAME_PAYMENT);
	$DB_PAYMENT->setCharset('utf8');
}

if (!isset($DB_PAYMENT) || !$DB_PAYMENT || !($rs_charset = $DB_PAYMENT->Execute('SHOW VARIABLES LIKE ?', array('character_set_database')))) {
	header('Content-Type: text/plain; charset=utf-8');
	die('Уебсайтът не може да осъществи връзка с базата данни. Моля изчакайте няколко минути, докато отстраняваме проблема.');
}

?>
