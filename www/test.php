<?php
include('Net/SSH2.php');
include('Crypt/RSA.php');

$ssh = new Net_SSH2('lois.novahost.bg', 22222);
$key = new Crypt_RSA();
$key->loadKey(file_get_contents('/var/www/.ssh/id_rsa_hlds'));
if (!$ssh->login('root', $key)) {
    exit('Login Failed');
}


var_dump($ssh->exec('uname -a'));

	// $ssh_ip = 'lois.novahost.bg';
	// $ssh_connection = ssh2_connect($ssh_ip, 22222, array('hostkey'=>'ssh-rsa'));
	// ssh2_auth_pubkey_file($ssh_connection, 'root', '/var/www/.ssh/id_rsa_hlds.pub', '/var/www/.ssh/id_rsa_hlds');
	// $sftp = ssh2_sftp($ssh_connection);
	// ssh2_sftp_unlink($sftp, '/home/servers/cstrike/1/cstrike/addons/amxmodx/configs/plugins.ini');
	//$fp = fopen('ssh2.sftp://' . $sftp . '/home/servers/cstrike/1/cstrike/addons/amxmodx/configs/plugins.ini', 'w');
	//fwrite($fpzp, $plugin_zp_text_fwrite);
	//fclose($fpzp);
//require('../includes/core.php');
/* require('../includes/server_query.php');
var_dump($rcon->getPlayers());

foreach ($rcon->getPlayers() as $player) {
	echo $player['Name'];
} */
?>