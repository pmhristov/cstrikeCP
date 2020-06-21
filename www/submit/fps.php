<?php
session_start();
require_once('../../includes/core.php');

$price = $fps_labels[$_POST['fps_rating']][0];
$status = 0;

if ($server_balance - $price >= 0) {
	
	serviceCharge('fps', $_POST['fps_rating'], $_SESSION['id'], $price);
	$DB->Execute('UPDATE `servers` SET `fps` = ? WHERE `id` = ?', array($_POST['fps_rating'], $_SESSION['id']));
	$_SESSION['fps'] = $_POST['fps_rating'];
	serverRestart();
	$status = 1;
	$msg = $returnedData_msg['request'];
}
else {
	$msg = 'Грешка: Вашия баланс не е достатъчен за да активирате тази услуга. Можете да заредите вашия баланс от <a href="/balance-add">тук.</a>';
}


echo json_encode(
	array(
		'status' => $status,
		'msg' => $msg,
	)
);
?>
