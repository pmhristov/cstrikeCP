<?php
session_start();
require_once('../../includes/core.php');

$price = 2.40;
$status = 0;
$plugins_add = 5;

if ($server_balance - $price >= 0) {
	serviceCharge('plugins-more', $_SESSION['id'], $price);
	$DB->Execute('UPDATE `servers` SET `plugins_limit` = `plugins_limit` + ? WHERE `id` = ?', array(5, $_SESSION['id']));
	$_SESSION['plugins_limit'] = $_SESSION['plugins_limit'] + $plugins_add;
	$status = 1;
	$msg = $returnedData_msg['request'];
}
else {
	$msg = 'Грешка: Вашия баланс не е достатъчен за да активирате тази услуга. Можете да заредите вашия баланс от <a href="/balance?page=add">тук.</a>';
}

echo json_encode(
	array(
		'status' => $status,
		'msg' => $msg,
	)
);

?>
