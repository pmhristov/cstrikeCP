<?php
session_start();
require_once('../../includes/core.php');

$price = 4.80;
$status = 0;

if ($_SESSION['players'] < $players_limit[$_SESSION['plan']]) {
	if ($server_balance - $price >= 0) {
		serviceCharge('slots', $_SESSION['id'], $price);
		$DB->Execute('UPDATE `servers` SET `players` = `players` + 2 WHERE `id` = ?', array($_SESSION['id']));
		$players = $DB->GetOne('SELECT `players` FROM `servers` WHERE `id` = ?', array($_SESSION['id']));
		$_SESSION['players'] = $players;
		serverRestart();
		$status = 1;
		$msg = $returnedData_msg['request'];
	}
	else {
		$msg = 'Грешка: Вашия баланс не е достатъчен за да активирате тази услуга. Можете да заредите вашия баланс от <a href="/balance-add">тук.</a>';
	}
}
else {
	$msg = 'Грешка: достигнали сте максималния брой игрални слотове за вашия план. Повече информация можете да прочетете <a  href="/plan">тук</a>.';
}


echo json_encode(
	array(
		'status' => $status,
		'msg' => $msg,
	)
);
?>
