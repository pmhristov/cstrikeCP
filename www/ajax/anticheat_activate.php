<?php
session_start();

require_once('../../includes/core.php');

$id = trim($_GET['id']);
 
$anticheat = $DB->GetRow('SELECT * FROM `anticheat` WHERE `id` = ?', $id);

if ($anticheat['server_id'] == $_SESSION['id']) {
	$DB->Execute('UPDATE `anticheat` SET `active` = ? WHERE `id` = ?', array(1, $id));
	echo  'Анти-чиит системата (' . $anticheat['anticheat'] . ') е успешно активирана.';
	cfgReload();
	cfgRestart();
}
else {
	echo 'Грешка: Невалидно ID';
}


?>
