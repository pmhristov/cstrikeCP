<?php
session_start();
require_once('../../includes/core.php');

$mode = trim($_POST['mode']);
$status = 0;

if ($mode != '') {
	$server_info = $DB->GetRow('SELECT * FROM `servers` WHERE `id` = ?', $_SESSION['id']);
 	if ($server_info['mode'] != $mode) {
		//serviceCharge('mode', $mode_labels[$mode][1], $_SESSION['id'], $mode_labels[$mode][2]);
		$redirects = $DB->GetAll('SELECT * FROM `redirects` WHERE `server_id` = ?', $_SESSION['id']);
		$DB->Execute('UPDATE `servers` SET `mode` = ? WHERE `id` = ?', array($mode, $_SESSION['id']));
		$_SESSION['mode'] = $mode;
		$login_ssh2->exec('/usr/local/cstrike/mode.sh ' . $_SESSION['id'] . ' ' . $mode  . ' > /dev/null');
	
		$DB->Execute('INSERT INTO `actions` SET `service` = ?, `action` = ?, `service_id` = ?', array('cstrike-server', 'mode - ' . $mode, $_SESSION['ip'] . ':' . $_SESSION['port']));
		
		$plugins = $DB->GetAll('SELECT * FROM `plugins` WHERE `server_id` = ?', array($_SESSION['id']));
		
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
		
		$redirects = $DB->GetAll('SELECT * FROM `redirects` WHERE `server_id` = ?', $_SESSION['id']);
		if (!empty($redirects)) {
			$login_ssh2->exec('/usr/local/cstrike/plugin.sh ' . $_SESSION['id'] . ' xredirect');
		}
		
		//$DB->Execute('UPDATE `servers` SET `mode_used` = `mode_used` + 1 WHERE `id` = ?', array($_SESSION['id']));
		cfgReload();
		serverRestart();
		$status = 1;
		$msg = $returnedData_msg['request'];
	}
	else {
		$msg = 'Грешка: Този мод е вече активен, изберете друг.';
	}
}
else {
	$msg = 'Грешка: Моля изберете валиден мод';
}

echo json_encode(
	array(
		'status' => $status,
		'msg' => $msg,
	)
);
?>