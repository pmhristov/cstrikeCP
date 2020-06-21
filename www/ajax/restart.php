<?php
session_start();

require '../../includes/core.php';

 if ($_GET['restart'] == 'round') {
	$server->RconCommand('sv_restart 1');
	echo $returnedData_msg['request'];
}
elseif ($_GET['restart'] == 'map') {
	$changemap = $server->RconCommand('restart');
	echo $returnedData_msg['request'];
}
elseif ($_GET['restart'] == 'system') {
	$login_ssh2->exec('/usr/local/cstrike/restart.sh ' . $_SESSION['id']);
	echo $returnedData_msg['request'];
}
else {
	echo 'Невалидна заявка към сървъра. Свържете се с нас за повече информация.';
}
?>
