# rcon-client
GoldSrc RCON client written in PHP
## Usage
    <?php
    require __DIR__ . '/vendor/autoload.php';

    use User890104\RconClient;

    $rcon = new RconClient('example.com', '123456');

    // using the text protocol
    echo $rcon->exec('stats');
	
    // using the binary protocol
    var_dump($rcon->getInfo());
