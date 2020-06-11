<?php
session_start();
require_once('../../includes/core.php');

$DB->Execute('UPDATE `servers` SET `gamemenu` = ? WHERE `id` = ?', array(2, $_SESSION['id']));

$_SESSION['gamemenu'] = 2;
cfgReload();
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