<?php
session_start();

require_once('../../includes/core.php');

$id = trim($_GET['id']);
 
$plugin_details = $DB->GetRow('SELECT * FROM `plugins` WHERE `id` = ?', $id);

if ($plugin_details['server_id'] == $_SESSION['id']) {
	$plugin_description = $DB->GetRow('SELECT * FROM `pluginlist` WHERE `name` = ?', $plugin_details['plugin']);
	if (!empty($plugin_description['description'])) {
		echo $plugin_description['description'];
	}
	else {
		echo $plugin_description['url'];
	}
}
else {
	echo 'Грешка: Невалидно ID';
}


?>
