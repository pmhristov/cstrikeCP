<?php
session_start();

require_once('../../includes/core.php');

$id = trim($_GET['id']);
 
$plugin_details = $DB->GetRow('SELECT * FROM `plugins` WHERE `id` = ?', $id);

if ($plugin_details['server_id'] == $_SESSION['id']) {
	$DB->Execute('UPDATE `plugins` SET `active` = ? WHERE `id` = ?', array(0, $id));
	echo  'Плъгина (' . $plugin_details['plugin'] . ') е успешно деактивиран.';
	cfgReload();
	cfgRestart();
}
else {
	echo 'Грешка: Невалидно ID';
}


?>
