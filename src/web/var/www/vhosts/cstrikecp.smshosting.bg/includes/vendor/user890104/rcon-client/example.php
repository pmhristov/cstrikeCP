<?php
require __DIR__ . '/vendor/autoload.php';

use User890104\RconClient;

$rcon = new RconClient('example.com', '123456');

echo $rcon->exec('stats');
echo $rcon->exec('status');
echo $rcon->exec('users');
//echo $rcon->exec('maps *');

var_dump($rcon->getInfo());
var_dump($rcon->getPlayers());
//var_dump($rcon->getRules());
