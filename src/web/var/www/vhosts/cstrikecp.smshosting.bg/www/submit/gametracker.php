<?php
session_start();
require_once('../../includes/core.php');

$status = 0;
$price = 1.20;


foreach($_POST as $key =>$value){
	if (empty($_POST[$key])) {
		$msg = 'Грешка: Моля попълнете всички полета';
	}
}

if ($server_balance - $price > 0) {
	serviceCharge('gametracker', $_SESSION['id'], $price);
	$main_table = 'cstrike_servers_gametracker';
	$DB->AutoExecute($main_table, $_POST, 'INSERT');
	$last_id = $DB->Insert_ID();
	
	$DB->Execute('UPDATE `gametracker` SET `server_id` = ? WHERE `id` = ?', array($_SESSION['id'], $last_id));
	
	$action_records = array(
		'service' => 'cstrike-server',
		'action' => 'gametracker',
		'service_id' => $_SESSION['ip'] . ':' . $_SESSION['port'],
		'service_plan' => $_SESSION['plan'],
		'mail' => $_SESSION['mail'],
		'code' => $code,
	);		
	$DB->AutoExecute($action_table, $action_records, 'INSERT');

	$gt_dir = '/var/www/vhosts/gametracker.smshosting.bg/www/' . $_SESSION['ip'] . ':' . $_SESSION['port'];

	if (!file_exists($gt_dir)) {
		mkdir($gt_dir, 0700);
	}

	copy('/var/www/vhosts/gametracker.smshosting.bg/www/example.php', $gt_dir . '/' . $last_id . '.png.php');
	$status = 1;
	$msg = $returnedData_msg['request'];
}
else {
	$msg = 'Грешка: Вашия баланс не е достатъчен за да активирате тази услуга. Можете да заредите вашия баланс от <a href="/balance-add">тук.</a>';
}
?>