<?php

session_start();

require_once('../../includes/core.php');
require_once(INCLUDE_DIR . 'rcon.php');

$svpasswd = trim($_POST['svpasswd']);
$server->RconCommand('sv_password ' . htmlspecialchars($svpasswd));
$status = 1;
$msg = $returnedData_msg['request'];

echo json_encode(
	array(
		'status' => $status,
		'msg' => $msg,
	)
);
?>
