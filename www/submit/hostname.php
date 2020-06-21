<?php
session_start();
require_once('../../includes/core.php');

$newhostname = trim($_POST['newhostname']);

$DB->Execute('UPDATE `servers` SET `hostname` = ? WHERE `id` = ?', array($newhostname, $_SESSION['id']));
$server_details = $DB->GetRow('SELECT * FROM `servers` WHERE `id` = ?', $_SESSION['id']); 
$login_ssh2->exec('/usr/local/cstrike/restart.sh ' . escapeshellarg($server_details['id']));
$_SESSION['hostname'] = $newhostname;
$status = 1;
$msg = $returnedData_msg['request'];

echo json_encode(
	array(
		'status' => $status,
		'msg' => $msg,
	)
);
?>
