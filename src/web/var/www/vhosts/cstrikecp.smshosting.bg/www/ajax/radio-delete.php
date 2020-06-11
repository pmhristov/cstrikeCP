<?php
session_start();

require '../../includes/core.php';


$id = trim($_GET['id']);
 
$radio_details = $DB->GetRow('SELECT * FROM `radio` WHERE `id` = ?', $id);

if ($radio_details['server_id'] == $_SESSION['id']) {
	$DB->Execute('DELETE FROM `radio` WHERE `id` = ?', $id);
		cfgReload();
		serverRestart();		
	echo  'Онлаин радио сървъра е успешно премахната';
}
else {
	echo 'Грешка: Невалидно ID';
}


?>
