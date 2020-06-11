<?php
session_start();
require_once('../../includes/core.php');

$status = 0;

if (isset($_POST['mode_remove'])) {
	$mode_info = $DB->GetOne('SELECT `mode` FROM `servers` WHERE `id` = ?', $_SESSION['id']);
 	if ($mode_info != 1) {
		$DB->Execute('UPDATE `servers` SET `mode` = ? WHERE `id` = ?', array(1, $_SESSION['id']));
		$DB->Execute('DELETE FROM `admins` WHERE `server_id` = ?', $_SESSION['id']);
		$login_ssh2->exec('/usr/local/cstrike/mode.sh ' . $_SESSION['id'] . ' ' . 1);
		$_SESSION['mode'] = 1;
		cfgReload();
		serverRestart();
		$status = 1;
		$msg = $returnedData_msg['request'];
	}
	else {
		$msg = 'Грешка: вашия сървър не поддържа мод така, че няма как да бъде премахнат.';
	}
}
else {
	$msg = 'Грешка: грешна при изпращане на заявката. Моля свържете се с нас за повече информация.';
}

echo json_encode(
	array(
		'status' => $status,
		'msg' => $msg,
	)
);
?>