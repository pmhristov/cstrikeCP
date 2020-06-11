<?php
session_start();

require_once('../../includes/core.php');
require_once(INCLUDE_DIR . 'rcon.php');

if ($_SESSION['admins_custom'] == 1) {
	$login_sftp->delete('/home/servers/cstrike/' . $_SESSION['id'] . '/cstrike/addons/amxmodx/configs/users.ini');
	$login_sftp->put('/home/servers/cstrike/' . $_SESSION['id'] . '/cstrike/addons/amxmodx/configs/users.ini', trim($_POST['admins']));

	$server->RconCommand('amx_reloadadmins');
	$status = 1;
	$msg = $_POST['admins_custom'] . $returnedData_msg['request'];		
}
else {
$type = trim($_POST['type']);
$period = trim(preg_replace('/[^0-9]/', '', $_POST['period']));
$status = 0;


if ($type == 'a') {
	$name = trim($_POST['name']);
}

if ($type == 'ac') {
	$name = trim($_POST['name']);
}

if ($type == 'ab') {
	$name = trim($_POST['name']);
}

if ($type == 'ad') {
	$name = trim($_POST['name']);
}

$passwd = trim($_POST['passwd']);
$repasswd = trim($_POST['repasswd']);

$flags = str_split('abcdefghijklmnopqrstuz');
$flags_string = '';

foreach ($flags as $curr_flag) {
	if (!empty($_POST['flags_' . $curr_flag])) {
		$flags_string .= $curr_flag;
	}
}


if ($passwd == $repasswd) {
	if (!empty($type)) {
		if (!empty($flags_string)) {
			if ($period != '') {
				$admin_check = $DB->GetOne('SELECT COUNT(*) FROM `admins` WHERE `server_id` = ? AND `name` = ? ', array($_SESSION['id'], $name));
				if ($admin_check == 0) {
					if ($period == 0) {
						$expire_date = '0000-00-00';
					}
					else {
						$expire_date = CalculateDate($period);
					}
					
					$DB->Execute('INSERT INTO `admins` SET `server_id` = ?, `name` = ?, `passwd` = ?, `type` = ?, `flags` = ?, `expire` = ?', array($_SESSION['id'], $name, $passwd, $type, $flags_string, $expire_date));
					cfgReload();
					$server->RconCommand('amx_reloadadmins');
					$status = 1;
					$msg = $returnedData_msg['request'];			
				}
				else {
					$msg = 'Грешка: Този администратор вече съществува.';
				}
			}
			else {
				$msg = 'Въведете валиден период на изтичане.';
			}
		}	
		else {
			$msg = 'Грешка: Моля изберете флагове';
		}	
	}
	else {
		$msg = 'Грешка: Моля изберете тип на разпознаване.';
	}
}
else {
 $msg = 'Грешка: Двете пароли не съвпадат.';
}
}
echo json_encode(
	array(
		'status' => $status,
		'msg' => $msg,
	)
);
?>