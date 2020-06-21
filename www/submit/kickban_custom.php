<?php
session_start();
require_once('../../includes/core.php');

$status = 0;

if ($_POST['kickban_custom'] == 1) {
	$DB->Execute('UPDATE `servers` SET `kickban_custom` = ? WHERE `id` = ?', array(1, $_SESSION['id']));
	$_SESSION['kickban_custom'] = 1;
	serverRestart();
	$status = 1;
	$msg = $returnedData_msg['request'];
}
else {
	$DB->Execute('UPDATE `servers` SET `kickban_custom` = ? WHERE `id` = ?', array(0, $_SESSION['id']));
	$_SESSION['kickban_custom'] = 0;
	serverRestart();
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
