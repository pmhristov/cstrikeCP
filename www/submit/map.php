<?php

session_start();

require_once('../../includes/core.php');
require_once(INCLUDE_DIR . 'rcon.php');

$map = trim($_POST['map']);

if (!empty($map)) {
	if (isset($_POST['map_default'])) {
		$DB->Execute('UPDATE `servers` SET `map` = ? WHERE `id` = ?', array($map, $_SESSION['id']));
		$_SESSION['map'] = $map;
		serverRestart();
		$status = 1;
		$msg = $returnedData_msg['request'];
	}
	else {
		$server->RconCommand('changelevel ' . $map);
		$status = 1;
		$msg = $returnedData_msg['request'];
	}
}
else {
	$msg = 'Изберете валидна карта.';
}
echo json_encode(
	array(
		'status' => $status,
		'msg' => $msg,
	)
);
?>
