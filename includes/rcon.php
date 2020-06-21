<?php
if ($_SESSION['game'] == 'hlds') {
	require_once('libs/rcon_protocol_hlds.php');
	$server = new rcon(); 

	if($server->connect($_SESSION['ip'], $_SESSION['port'], $_SESSION['passwd'])) { 
		if(!$server->connected)
		return $server->connected;
		$status = $server->RconCommand('status');
		$line = explode("\n", $status);
		$map = substr($line[3], strpos($line[3], ":") + 1);
		$players = trim(substr($line[4], strpos($line[4], ":") + 1));
		$active = explode(" ", $players);
		$result["map"] = trim(substr($map, 0, strpos($map, "at:")));
		$result["game"] = "Halflife";
		$result["activeplayers"] = $active[0];
		$result["maxplayers"] = substr($active[2], 1);
		return $result;
		$server->disconnect();
	}
}
elseif ($_SESSION['game'] == 'srcds') {
	require_once('libs/rcon_protocol_srcds.php');
	$server = new Rcon();
	if($server->connect($_SESSION['ip'], $_SESSION['port'], $_SESSION['passwd'])) { 
		 
		$status = $server->rcon_command('79.98.108.70', '27050', 'xxxxxxxxxxxxx', 'status');
		$line = explode("\n", $status);
		$map = substr($line[4], strpos($line[4], ":") + 1);
		
		$players = trim(substr($line[5], strpos($line[5], ":") + 1));
		$active = explode(" ", $players);
		$result["map"] = trim(substr($map, 0, strpos($map, "at:")));
		$result["activeplayers"] = $active[0];
		$result["maxplayers"] = substr($active[2], 1);
		return $result;
	} 	
}
else {
	
}
	


?>
