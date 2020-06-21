<?php
session_start();
require_once('../../includes/core.php');

$status = 0;

if (isset($_POST)) {
	$server->RconCommand('csstats_reset 1');	
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