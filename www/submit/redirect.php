<?php
session_start();
require_once('../../includes/core.php');

$hostname = trim($_POST['hostname']);
$addr = trim($_POST['addr']);
$status = 0;


if (!empty($hostname) && !empty($addr)) {
	$redirects = $DB->GetOne('SELECT COUNT(*) FROM `redirects` WHERE `server_id` = ?', $_SESSION['id']);
	if ($redirects == 0) {
		$login_ssh2->exec('/usr/local/cstrike/plugin.sh ' . $_SESSION['id'] . ' xredirect');
	}
	$DB->Execute('INSERT INTO `redirects` SET `server_id` = ?, `hostname` = ?, `addr` = ?', array($_SESSION['id'], trim($hostname), trim($addr)));
	cfgReload();
	cfgRestart();
	$status = 1;
	$msg = $returnedData_msg['request'];
}
else {
	$msg = 'Грешка: попълнете всички полета';
}

echo json_encode(
	array(
		'status' => $status,
		'msg' => $msg,
	)
);

?>
