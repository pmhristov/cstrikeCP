<?php
require __DIR__ . '/vendor/autoload.php';

use User890104\RconClient;

$rcon = new RconClient($_SESSION['ip'], $_SESSION['passwd'], $_SESSION['port'], .05);

$serverinfo = $rcon->getInfo();
