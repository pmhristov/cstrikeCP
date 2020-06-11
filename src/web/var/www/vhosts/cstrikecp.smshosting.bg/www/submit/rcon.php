<?php

session_start();
require_once('../../includes/core.php');

$old_rcon = trim($_POST['oldrcon']);
$new_rcon = trim($_POST['newrcon']);
$renewrcon = trim($_POST['renewrcon']);
$status = 0;

if (!empty($new_rcon)) {
	if (isValidPasswd($new_rcon)) {
		if ($new_rcon == $renewrcon) {
			$rcon_check = $DB->GetOne('SELECT `passwd` FROM `servers` WHERE `id` = ? AND `passwd` = ?', array($_SESSION['id'], $old_rcon));
			if (!empty($rcon_check)) {
				$DB->Execute('UPDATE `servers` SET `passwd` = ? WHERE `id` = ?', array($new_rcon, $_SESSION['id']));
				$_SESSION['passwd'] = $new_rcon;
				serverRestart();
				$status = 1;
				$msg = $returnedData_msg['request'];
			}
			else {
				$msg = 'Грешка: грешна RCON парола';
			}	
		}
		else {
			$msg = 'Грешка: двете пароли не съвпадат !';
		}
	}
	else {
		$msg = 'Паролата трябва да е минимум 6 символа и да съдържа букви и цифри.';
	}
}
else {
	$msg = 'Грешка: паролата не може да бъде празна.';
}
echo json_encode(
	array(
		'status' => $status,
		'msg' => $msg,
	)
);
?>