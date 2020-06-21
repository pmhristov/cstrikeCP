<?php

session_start();
require_once('../../includes/core.php');
require_once(INCLUDE_DIR . 'rcon.php');


if ($_SESSION['kickban_custom'] == 1) {
	$sftp = ssh2_sftp($ssh_connection);
	$login_sftp->delete('/home/servers/cstrike/' . $_SESSION['id'] . '/cstrike/listip.cfg');
	$login_sftp->put('/home/servers/cstrike/' . $_SESSION['id'] . '/cstrike/listip.cfg', trim($_POST['kickbans']). "\n");
	$server->RconCommand('exec listip.cfg');
	$status = 1;
	$msg = $returnedData_msg['request'];		
}
else {

	$player = explode('::;;:', $_POST['player']);
	$player_name = $player[0];
	$ip = $player[1];

	$reason = trim($_POST['reason']);

	if (isset($_POST['ban_check']) && $_POST['ban_check'] == 'yes') {
		$server->RconCommand('kick ' . $player_name . ' ' . $reason);
		$ban_time = trim($_POST['ban_time']);
		$server->RconCommand('addip ' . $ban_time . ' ' . $ip);
		$status = 1;
		$msg = $returnedData_msg['request'];
	}
	else {
		$server->RconCommand('kick ' . $player_name . ' ' . $reason);
		$status = 1;
		$msg = $returnedData_msg['request'];
	}
}
echo json_encode(
	array(
		'status' => $status,
		'msg' => $msg,
	)
);

?>