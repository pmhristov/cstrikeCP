<?php

session_start();

require_once('../../includes/core.php');
require_once(INCLUDE_DIR . 'rcon.php');

$message = trim($_POST['msg']);
$status = 0;

if ($message != '') {
	if (strlen($message) < 150) {
		$server->RconCommand('say ' . $message);
		$status = 1;
		$msg = $returnedData_msg['request'];
	}
	else {
		$msg = 'Грешка: текста не може да бъде по - дълъг от 150 символа.';
	}
}
else {
	$msg = 'Грешка: съобщението не може да бъде празно.';
}

echo json_encode(
	array(
		'status' => $status,
		'msg' => $msg,
	)
);
?>