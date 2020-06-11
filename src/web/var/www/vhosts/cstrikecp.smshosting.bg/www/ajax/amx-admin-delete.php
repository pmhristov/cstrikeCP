<?php
session_start();

require '../../includes/core.php';
require_once(INCLUDE_DIR . 'rcon.php');

$id = trim($_GET['id']);
 
$admin_details = $DB->GetRow('SELECT * FROM `admins` WHERE `id` = ?', $id);

if ($admin_details['server_id'] == $_SESSION['id']) {
	$DB->Execute('DELETE FROM `admins` WHERE `id` = ?', $id);
	echo  'Потребителя (' . $admin_details['name'] . ') е успешно изтрит.';
	cfgReload();
	$server->RconCommand('amx_reloadadmins');
}
else {
	echo 'Грешка: Невалидно ID';
}
?>
