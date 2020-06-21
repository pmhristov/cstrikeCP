<?php
session_start();
require_once('../../includes/core.php');

$DB->Execute('UPDATE `servers` SET `gamemenu` = ? WHERE `id` = ?', array(1, $_SESSION['id']));

$_SESSION['gamemenu'] = 1;
$login_ssh2->exec('/usr/local/cstrike/plugin.sh ' . $_SESSION['id'] . ' gamemenu');
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