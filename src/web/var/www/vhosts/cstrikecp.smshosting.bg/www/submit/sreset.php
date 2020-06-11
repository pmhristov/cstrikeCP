<?php
session_start();
require_once('../../includes/core.php');

	$status = 0;
	
	$login_ssh2->exec('/usr/local/cstrike/reset.sh ' . $_SESSION['id'] . " '" . escapeshellarg(crypt($_SESSION['passwd_cp'])) . "'");
	$login_ssh2->exec('/usr/local/cstrike/mode.sh ' . $_SESSION['id'] . ' ' . $_SESSION['mode']  . ' > /dev/null');
 	
	$plugins = $DB->GetAll('SELECT * FROM `plugins` WHERE `server_id` = ? AND `active` = ?', array($_SESSION['id'], 1));
	
	foreach ($plugins as $plugin) {
		$login_ssh2->exec('/usr/local/cstrike/plugin.sh ' . $_SESSION['id'] . ' ' . $plugin['plugin']);
	}
	
	if ($_SESSION['anticheat'] != '') {
		$login_ssh2->exec('/usr/local/cstrike/anticheat.sh ' . $_SESSION['id'] . ' 1 ' . $_SESSION['anticheat']);
	}

	if ($_SESSION['amx_bans'] == 1) {
		$login_ssh2->exec('/usr/local/cstrike/plugin.sh ' . $_SESSION['id'] . ' system_amxbans');
	}
	
	if ($_SESSION['radio'] == 1) {
		$login_ssh2->exec('/usr/local/cstrike/plugin.sh ' . $_SESSION['id'] . ' online_radio');
	}
	
	if ($_SESSION['gamemenu'] == 1) {
		$login_ssh2->exec('/usr/local/cstrike/plugin.sh ' . $_SESSION['id'] . ' gamemenu');
	}
	
	if ($_SESSION['antihlbrute'] == 1) {
		$login_ssh2->exec('/usr/local/cstrike/plugin.sh ' . $_SESSION['id'] . ' antihlbrute');
	}
	
	$redirects = $DB->GetAll('SELECT * FROM `redirects` WHERE `server_id` = ?', $_SESSION['id']);
	if (!empty($redirects)) {
		$login_ssh2->exec('/usr/local/cstrike/plugin.sh ' . $_SESSION['id'] . ' xredirect');
	}
		
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