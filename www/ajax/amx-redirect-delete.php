<?php
session_start();
require '../../includes/core.php';

$id = trim($_GET['id']);


$DB->Execute('DELETE FROM `redirects` WHERE `id` = ?', $id);
echo 'Пренасочването беше премахнато успешно.';
cfgReload();
cfgRestart();

?>
