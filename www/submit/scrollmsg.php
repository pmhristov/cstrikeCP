<?php
session_start();

require_once('../../includes/core.php');

$scrollmsg_text = trim(stripslashes($_POST['scrollmsg_text']));
$scrollmsg_time = $_POST['scrollmsg_time'];
$status = 0;


if (!empty($scrollmsg_text)) {
	$DB->Execute('UPDATE `servers` SET `scrollmsg_text` = ?, `scrollmsg_time` = ? WHERE `id` = ?', array($scrollmsg_text, $scrollmsg_time, $_SESSION['id']));
	$login_ssh2->exec('/usr/local/cstrike/scrollmsg.sh ' . $_SESSION['id']);
	$login_sftp->put('/home/servers/cstrike/' . $_SESSION['id'] . '/cstrike/addons/amxmodx/configs/amxx.cfg', 'amx_scrollmsg "' . $scrollmsg_text . '" ' . $scrollmsg_time . "\n", NET_SFTP_RESUME);
	cfgRestart();
	$status = 1;	
	$msg = $returnedData_msg['request'];
}
else {
	$msg = 'Грешка: въведете текст';
}


echo json_encode(
	array(
		'status' => $status,
		'msg' => $msg,
	)
);
?>
