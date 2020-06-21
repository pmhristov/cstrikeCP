<?php
session_start();
require_once('../../includes/core.php');

$action = 'anticheat';
$type = trim($_POST['type']);
$status = 0;

$anticheat = $DB->GetRow('SELECT * FROM `anticheat` WHERE `server_id` = ? AND `anticheat` = ?', array($_SESSION['id'], $type));


	if ($type != '') {
		if (empty($anticheat)) {
				$login_ssh2->exec('/usr/local/cstrike/anticheat.sh ' . $_SESSION['id'] . ' 1 ' . $type . ' > /dev/null');
				$DB->Execute('INSERT INTO `anticheat` SET `server_id` = ?, `anticheat` = ?, `active` = ?', array($_SESSION['id'], $type, 1));					
				cfgReload();
				cfgRestart();
				$status = 1;
				$msg = $returnedData_msg['request'];	
		}
		else {
			$msg = 'Грешка: Тази анти-чиит система вече съществува към вашия сървър.';
		}
	}
	else {
		$msg = 'Грешка: изберете система';
	}	

echo json_encode(
	array(
		'status' => $status,
		'msg' => $msg,
	)
);

?>