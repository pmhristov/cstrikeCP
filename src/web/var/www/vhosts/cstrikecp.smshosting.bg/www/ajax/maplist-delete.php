<?php
session_start();

require '../../includes/core.php';


$id = trim($_GET['id']);
 
$map_details = $DB->GetRow('SELECT * FROM `maplist` WHERE `id` = ?', $id);

if ($map_details['server_id'] == $_SESSION['id']) {
	$DB->Execute('DELETE FROM `maplist` WHERE `id` = ?', $id);
	$get_maps = $DB->GetAll('SELECT * FROM `maplist` WHERE `server_id` = ?', $_SESSION['id']);
	
	$login_sftp->delete('/home/servers/cstrike/' . $_SESSION['id'] . '/cstrike/mapcycle.txt');
	$login_sftp->put('/home/servers/cstrike/' . $_SESSION['id'] . '/cstrike/mapcycle.txt', 'w');
	foreach ($get_maps as $map) {	
		$login_sftp->put('/home/servers/cstrike/' . $_SESSION['id'] . '/cstrike/mapcycle.txt', $map['map'] . "\n", NET_SFTP_RESUME);	
	}
	$server->RconCommand('restart');
	echo  'Картата ' . $map_details['map'] . ' е успешно премахната';
}
else {
	echo 'Грешка: Невалидно ID';
}


?>
