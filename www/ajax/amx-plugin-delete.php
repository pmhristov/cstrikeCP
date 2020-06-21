<?php
session_start();

require_once('../../includes/core.php');

$id = trim($_GET['id']);
  
$plugin_details = $DB->GetRow('SELECT * FROM `plugins` WHERE `id` = ?', $id);

if ($plugin_details['server_id'] == $_SESSION['id']) {
	$DB->Execute('DELETE FROM `plugins` WHERE `id` = ?', $id);
	$DB->Execute('UPDATE `servers` SET `plugins_used` = `plugins_used` - 1 WHERE `id` = ?', array($_SESSION['id']));
	echo  'Плъгина (' . $plugin_details['plugin'] . ') е успешно премахнат.';
	cfgReload();
	cfgRestart();
}
else {
	echo 'Грешка: Невалидно ID';
}


?>
