<?php
session_start();

require '../../includes/core.php';

 if ($_GET['action'] == 'start') {
	$login_ssh2->exec('/usr/local/cstrike/restart.sh ' . $_SESSION['id']);
	$DB->Execute('UPDATE `servers` SET `shutdown` = ? WHERE `id` = ?', array(0, $_SESSION['id']));
	echo $returnedData_msg['request'];
}
elseif ($_GET['action'] == 'stop') {
	$login_ssh2->exec('/usr/local/cstrike/stop.sh ' . $_SESSION['id']);
	$DB->Execute('UPDATE `servers` SET `shutdown` = ? WHERE `id` = ?', array(1, $_SESSION['id']));
	echo $returnedData_msg['request'];
}
else {
	echo 'Невалидна заявка към сървъра. Свържете се с нас за повече информация.';
}
?>
