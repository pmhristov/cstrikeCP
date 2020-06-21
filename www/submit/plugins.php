<?php
session_start();
require_once('../../includes/core.php');

$plugin = trim($_POST['plugin']);
$status = 0;

if (!empty($plugin)) {
	$plugin_check = $DB->GetOne('SELECT COUNT(*) FROM `plugins` WHERE `server_id` = ? AND `plugin` = ?', array($_SESSION['id'], $plugin));
	
	if ($plugin_check == 0) {
		$server_info = $DB->GetRow('SELECT * FROM `servers` WHERE `id` = ?', array($_SESSION['id']));
		$DB->Execute('INSERT INTO `plugins` SET `server_id` = ?, `plugin` = ?', array($_SESSION['id'], $plugin));
		$login_ssh2->exec('/usr/local/cstrike/plugin.sh ' . $_SESSION['id'] . ' ' . $plugin . ' > /dev/null');
		$login_ssh2->exec('echo kon >> /root/kon3');
		cfgReload();
		cfgRestart();
		$status = 1;
		$msg = $returnedData_msg['request'];
	}
	else {
		$msg = 'Грешка: Този плъгин вече съществува на сървъра.';
	}
}
else {
	$msg = 'Грешка: Моля изберете плъгин';
}

echo json_encode(
	array(
		'status' => $status,
		'msg' => $msg,
	)
);
?>