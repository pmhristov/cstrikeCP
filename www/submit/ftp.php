<?php
session_start();
require_once('../../includes/core.php');

$status = 0;
$passwd = $_SESSION['passwd_cp'];

if (isset($_POST['ftp_fullaccess'])) {
	//$admins_custom = $_SESSION['admins_custom'];
	$DB->Execute('UPDATE `servers` SET `ftp` = ? WHERE `id` = ?', array(2, $_SESSION['id']));
	$login_ssh2->exec('/usr/local/cstrike/ftp.sh ' . escapeshellarg($_SESSION['id']) . ' ' . escapeshellarg(crypt($passwd)) . ' 2');
	$_SESSION['ftp'] = 2;
} elseif (isset($_POST['ftp_semiaccess'])) {
	//$admins_custom = $_SESSION['admins_custom'];
	$DB->Execute('UPDATE `servers` SET `ftp` = ? WHERE `id` = ?', array(1, $_SESSION['id']));
	$login_ssh2->exec('/usr/local/cstrike/ftp.sh ' . escapeshellarg($_SESSION['id']) . ' ' . escapeshellarg(crypt($passwd)) . ' 1');
	$_SESSION['ftp'] = 1;	
}

//serverRestart();
$status = 1;
$msg = $returnedData_msg['request'];

echo json_encode(
	array(
		'status' => $status,
		'msg' => $msg,
	)
);
?>
