<?php
session_start();
require_once('../../includes/core.php');

$status = 0;

if (isset($_POST)) {
	$login_ssh2->exec('/usr/local/cstrike/resetstats.sh ' . $_SESSION['id']);
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
