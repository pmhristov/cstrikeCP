<?php
session_start();
require_once('../../includes/core.php');

$DB->Execute('UPDATE `servers` SET amx_bans = ?, `amx_bans_host` = ?, `amx_bans_db` = ?, `amx_bans_dbuser` = ?, `amx_bans_dbpasswd` = ? WHERE `id` = ?', array(2, '', $db, '', '', $_SESSION['id']));

$_SESSION['amx_bans'] = 2;
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