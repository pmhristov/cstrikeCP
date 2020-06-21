<?php
header('Content-Type: text/plain; charset=utf-8');

session_start();
require_once('../../includes/core.php');
require_once(INCLUDE_DIR . 'rcon.php');

$status = 0;

$server_cmd = trim($_POST['server_cmd']);

if (strlen($server_cmd) < 150) {
	if (!empty($server_cmd) && false === strpos('rcon_password ', $server_cmd) && false === strpos('quit ', $server_cmd) && false === strpos('exit ', $server_cmd)) {
		$status = 1;
		$msg = 'командата беше изпълена успешно !' . "<br /><pre>" . $server->RconCommand($server_cmd) . '</pre>';
	}
	else {
		$msg = 'Грешка: Невалидна команда';
	}
}
else {
	$msg = 'Грешка: текста не може да бъде по - дълъг от 150 символа.';
}

echo json_encode(
	array(
		'status' => $status,
		'msg' => $msg,
	)
);
?>
