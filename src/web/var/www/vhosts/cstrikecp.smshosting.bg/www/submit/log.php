<?php
session_start();
require_once('../../includes/core.php');

$login_ssh2->exec('/usr/local/cstrike/clearlog.sh ' . escapeshellarg($_SESSION['id']));		
$status = 1;
$msg = $returnedData_msg['request'];

echo json_encode(
	array(
		'status' => $status,
		'msg' => $msg,
	)
);
?>