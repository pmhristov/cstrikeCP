<?php
require_once('/var/www/adodb/adodb.inc.php'); // ADOdB database abstraction layer
 
$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;

if (defined('DB_TYPE') && defined('DB_USER') && defined('DB_PASS') && defined('DB_HOST') && defined('DB_NAME') && defined('DB_OPTIONS')) {
	$DB = @NewADOConnection(DB_TYPE . '://' . DB_USER . ':' . DB_PASS . '@' . DB_HOST . '/' . DB_NAME . '?' . DB_OPTIONS);
}

if (!isset($DB) || !$DB || !($rs_charset = $DB->Execute('SHOW VARIABLES LIKE ?', array('character_set_database')))) {
	header('Content-Type: text/plain; charset=utf-8');
	die('Здравейте' . "\n\n" . 'Уебсайтът не може да осъществи връзка с базата данни. Моля изчакайте няколко минути, докато отстраняваме проблема.' . "\n" . 'Ако все още нямате достъп, моля информирайте отдела за поддръжка на e-mail адрес: support@smshosting.bg' . "\n\n" . 'Благодарим Ви!');
}
else {
	$DB->Execute('SET NAMES ?', array($rs_charset->fields['Value']));
	$rs_charset->Close();
}

if (defined('DB_TYPE_PAYMENT') && defined('DB_USER_PAYMENT') && defined('DB_PASS_PAYMENT') && defined('DB_HOST_PAYMENT') && defined('DB_NAME_PAYMENT') && defined('DB_OPTIONS_PAYMENT')) {
	$DB_PAYMENT = @NewADOConnection(DB_TYPE_PAYMENT . '://' . DB_USER_PAYMENT . ':' . DB_PASS_PAYMENT . '@' . DB_HOST_PAYMENT . '/' . DB_NAME_PAYMENT . '?' . DB_OPTIONS_PAYMENT);
}

if (!isset($DB_PAYMENT) || !$DB_PAYMENT || !($rs_charset = $DB_PAYMENT->Execute('SHOW VARIABLES LIKE ?', array('character_set_database')))) {
	header('Content-Type: text/plain; charset=utf-8');
	die('Здравейте' . "\n\n" . 'Уебсайтът не може да осъществи връзка с базата данни. Моля изчакайте няколко минути, докато отстраняваме проблема.' . "\n" . 'Ако все още нямате достъп, моля информирайте отдела за поддръжка на e-mail адрес: support@smshosting.bg' . "\n\n" . 'Благодарим Ви!');
}
else {
	$DB_PAYMENT->Execute('SET NAMES ?', array($rs_charset->fields['Value']));
	$rs_charset->Close();
}
?>
