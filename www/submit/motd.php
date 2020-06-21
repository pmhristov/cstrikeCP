<?php
session_start();

require_once('../../includes/core.php');

$motd_text = stripslashes($_POST['motd_text']);
$status = 0;

if (!empty($motd_text)) {
	$DB->Execute('UPDATE `servers` SET `motd` = ? WHERE `id` = ?', array($motd_text, $_SESSION['id']));
	$login_sftp->delete('/home/servers/cstrike/' . $_SESSION['id'] . '/cstrike/motd.txt');
	$login_sftp->put('/home/servers/cstrike/' . $_SESSION['id'] . '/cstrike/motd.txt', $motd_text);
	$_SESSION['motd'] = $motd_text;
	$status = 1;
	$msg = $returnedData_msg['request'];	
}
else {
	$msg = 'Грешка: Моля въведете MOTD код';
}

echo json_encode(
	array(
		'status' => $status,
		'msg' => $msg,
	)
);
?>
