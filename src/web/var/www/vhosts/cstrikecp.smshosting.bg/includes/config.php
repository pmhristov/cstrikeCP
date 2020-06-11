<?php
// DATABASE //

define('DB_TYPE', 'mysqli');
define('DB_USER', 'cstrikecp');
define('DB_PASS', 'xxxxxxxxxxxxxxxxxx');
define('DB_HOST', 'localhost');
define('DB_NAME', 'cstrikecp');
define('DB_OPTIONS', '');

define('DB_TYPE_PAYMENT', 'mysqli');
define('DB_USER_PAYMENT', 'payment');
define('DB_PASS_PAYMENT', 'xxxxxxxxxxxxxxxxxxxx');
define('DB_HOST_PAYMENT', 'localhost');
define('DB_NAME_PAYMENT', 'payment');
define('DB_OPTIONS_PAYMENT', '');

define('ROOT_DIR', '/var/www/vhosts/cstrikecp.smshosting.bg/');

define('INCLUDE_DIR', ROOT_DIR . 'includes/');
define('TEMPLATES_DIR', ROOT_DIR . 'templates/');
define('INCLUDE_LIB_DIR', INCLUDE_DIR . 'libs/');
define('WWW_DIR', ROOT_DIR . 'www/');
define('WWW_INCLUDE_DIR', WWW_DIR . 'includes/');

define('SMTP_HOST', 'mail.example.com');
define('SMTP_PORT', 465);
define('SMTP_USER', 'mail@example.com');
define('SMTP_PASS', 'xxxxxxxxxxxxx');
define('SMTP_FROM', 'mail@example.com');

$nodes = array(
	1 => 'lois.novahost.bg',
	//2 => 'cleveland.novahost.bg',
	2 => 'lois.novahost.bg',
);

define('DATE', date('Y-m-d'));

setlocale(LC_TIME, 'bg_BG.utf8');

$currency_BGN_to_EUR = .5112918811962185;

$balance_table = 'cstrike_servers_balance';

$ftp_hostname = 'csftp.smshosting.bg';
?>
