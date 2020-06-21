<?php
session_start();
require_once('../../includes/core.php');

$status = 0;
$price = 1.20;

if (isset($_POST['form_activation'])) {
	if ($_SESSION['balance'] - $price >= 0) {
		serviceCharge('cvars', $_SESSION['id'], $price);
		$DB->Execute('UPDATE `servers` SET `cvars` = ? WHERE `id` = ?', array(1, $_SESSION['id']));
		$status = 1;
		$msg = $returnedData_msg['request'];
	}
	else {
		$msg = 'Грешка: Вашия баланс не е достатъчен за да активирате тази услуга. Можете да заредите вашия баланс от <a href="/balance-add">тук.</a>';
	}
}
else {
	
	foreach ($_POST as $key => $value) {
		$login_sftp->put('/home/servers/cstrike/' . $_SESSION['id'] . '/cstrike/personal.cfg', $key .  ' ' . '"' . $value . '"' . "\n", NET_SFTP_RESUME);
	}
	$login_ssh2->exec('/usr/local/cstrike/restart.sh ' . escapeshellarg($_SESSION['id']));
	$status = 1;
	$msg = $returnedData_msg['request'];
}

echo json_encode(
	array(
		'status' => $status,
		'msg' => $msg,
	)
);
?>
