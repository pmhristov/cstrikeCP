<?php
session_start();
require_once('../../includes/core.php');

$status = 0;

if (isset($_POST['radio_activate'])) {
	$DB->Execute('UPDATE `servers` SET `radio` = ? WHERE `id` = ?', array(1, $_SESSION['id']));
	$_SESSION['radio'] = 1;
	$login_ssh2->exec('/usr/local/cstrike/plugin.sh ' . $_SESSION['id'] . ' online_radio');
	cfgReload();
	serverRestart();			
	$status = 1;
	$msg = $returnedData_msg['request'];	
}

if (!isset($_POST['radio_activate'])) {

	$name = trim($_POST['radio_name']);
	$addr = trim($_POST['radio_addr']);
	
	if (!empty($name) || !empty($addr)) {
		$DB->Execute('INSERT INTO `radio` SET `server_id` = ?, `name` = ?, `addr` = ?', array($_SESSION['id'], $name, $addr));
		cfgReload();
		serverRestart();
		$status = 1;
		$msg = $returnedData_msg['request'];
	}
	else {
		$msg = 'Грешка: моля попълнете всички полета!';
	}
}

echo json_encode(
	array(
		'status' => $status,
		'msg' => $msg,
	)
);
?>