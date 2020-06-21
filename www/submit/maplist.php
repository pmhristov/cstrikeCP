<?php
session_start();

require_once('../../includes/core.php');
require_once(INCLUDE_DIR . 'rcon.php');

$map = trim($_POST['map']);
$status = 0;

if (!empty($map)) {
	$map_check = $DB->GetOne('SELECT COUNT(*) FROM `maplist` WHERE `server_id` = ? AND `map` = ? ', array($_SESSION['id'], $map));

	if ($map_check == 0) {
		$DB->Execute('
		INSERT INTO
			`maplist`
		SET
			`server_id` = ?,
			`map` = ?',
		array(
			$_SESSION['id'],
			$map
		));

		$maps = $DB->GetAll('SELECT * FROM `maplist` WHERE `server_id` = ? ORDER BY `id`', $_SESSION['id']);

		$login_sftp->delete('/home/servers/cstrike/' . $_SESSION['id'] . '/cstrike/mapcycle.txt');
		foreach ($maps as $map_val) {				
			$login_sftp->put('/home/servers/cstrike/' . $_SESSION['id'] . '/cstrike/mapcycle.txt', $map_val['map'] . "\n", NET_SFTP_RESUME);
		}
		$server->RconCommand('restart');
		$status = 1;
		$msg = $returnedData_msg['request'];
	}
	else {
		$msg = 'Грешка: тази карта вече съществува в списъка.';
	}
}
else {
	$msg = 'Грешка: изберете карта';
}

echo json_encode(
	array(
		'status' => $status,
		'msg' => $msg,
	)
);
?>
