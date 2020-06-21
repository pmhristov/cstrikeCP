<?php
session_start();
require_once('../../includes/core.php');


$status = 0;


$DB->Execute('UPDATE `servers` SET `master` = ? WHERE `id` = ?', array(1, $_SESSION['id']));
$_SESSION['master'] = 1;
serverRestart();
$status = 1;
$msg = $returnedData_msg['request'];


echo json_encode(
	array(
		'status' => $status,
		'msg' => $msg,
	)
);
?>
