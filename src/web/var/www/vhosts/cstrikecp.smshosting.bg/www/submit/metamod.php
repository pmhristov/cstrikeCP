<?php
session_start();
require_once('../../includes/core.php');

$status = 0;

if (isset($_POST['metamod_activate'])) {
	$DB->Execute('UPDATE `servers` SET `metamod` = ? WHERE `id` = ?', array(1, $_SESSION['id']));
	serverRestart();
	$status = 1;
	$msg = $returnedData_msg['request'];
}
else {
	$DB->Execute('UPDATE `servers` SET `metamod` = ? WHERE `id` = ?', array(0, $_SESSION['id']));
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
