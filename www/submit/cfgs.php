<?php

session_start();
require_once('../../includes/core.php');
require_once(INCLUDE_DIR . 'rcon.php');

$cfg = trim($_POST['cfg']);
$server->RconCommand('exec ' . $cfg . '.cfg');

$status = 1;
$msg = $returnedData_msg['request'];

echo json_encode(
	array(
		'status' => $status,
		'msg' => $msg,
	)
);
?>
