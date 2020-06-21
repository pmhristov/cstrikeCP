![cstrikeCP Logo](https://smshosting.bg/images/cstrikecp-logo-new.png)
 
 Counter Strike 1.6 Web panel / Launcher / Payments-Earning system - Linux
 
 This is a system that i developed for some years (for previously counter-strike hosting company named smshosting.bg) and im giving it for free, no license, no decoded files, no malware, no ads. If propper installation, there should be no issues, but if you need help, contact me here or at peterhristov89@gmail.com

# Server side Download URL
!!!You need this!!!

https://drive.google.com/file/d/1d298bIk5JcI-0PUEUeBJH0IenIvR4fe5/view?usp=sharing

 
 ## Features
* Create/delete/launch/stop/modify counter strike servers
* Billing and payments
* CPU affinity for CS servers
* MySQL as data backend
* Logging
* Support Steam & Non-Steam Protocols
* Global authorization system based on roles
* Add/delete counter strike modes, maps, plugins, metamods etc...
* Over 30 pre-configured mods (included)
* Over 560 pre-configured plugins (included, most of them even tested and working properly )
* Various anticheat programs
* Online radio integration
* Add account balance which allows you to make money from your clients/customers
* Integrated firewall
* Many scripts and tools for easier management

## Requirements
* Apache/Nginx Web servers
* MySQL
* PHP 5.6+ (For the control panel) | PHP 5.3+ (For the server)
* Some FTP server (ProFTPD as example)
* Basic linux skills

## Content tree
* this repository - the control panel itself
* server - ready-to-use server with reHLDS and a bunch of addons and module
* Crontabs
* bash scripts that serve for the control panel
* bunch of useful scripts for easier work flow if you need to do mass actions

## Basic configs for edit

* includes/config.php

```php
<?php
// DATABASE //

define('DB_TYPE', 'mysqli');
define('DB_USER', 'cstrikecp');
define('DB_PASS', 'xxxxxxxxxxxxxxxxxxxx');
define('DB_HOST', 'localhost');
define('DB_NAME', 'cstrikecp');
define('DB_OPTIONS', '');

define('DB_TYPE_PAYMENT', 'mysqli');
define('DB_USER_PAYMENT', 'cstrikecp');
define('DB_PASS_PAYMENT', 'xxxxxxxxxxxxxxxxxxxx');
define('DB_HOST_PAYMENT', 'localhost');
define('DB_NAME_PAYMENT', 'cstrikecp');
define('DB_OPTIONS_PAYMENT', '');

define('ROOT_DIR', '/var/www/vhosts/cstrikecp.smshosting.bg/');

define('INCLUDE_DIR', ROOT_DIR . 'includes/');
define('TEMPLATES_DIR', ROOT_DIR . 'templates/');
define('INCLUDE_LIB_DIR', INCLUDE_DIR . 'libs/');
define('WWW_DIR', ROOT_DIR . 'www/');
define('WWW_INCLUDE_DIR', WWW_DIR . 'includes/');

define('SMTP_HOST', 'mail.example.com');
define('SMTP_PORT', 465);
define('SMTP_USER', 'mail@example.com');
define('SMTP_PASS', 'xxxxxxxxxxxxx');
define('SMTP_FROM', 'mail@example.com');

$nodes = array(
	1 => array(
		'hostname' => 'server1.example.com',
		'ipaddress' => '79.98.108.160',
		'ipaddress' => 'root',
		'port' => '22222',
		'privatekey' => 'rsa_testserver',
	),
	1 => array(
		'hostname' => 'server2.example.com',
		'ipaddress' => '79.98.108.160',
		'ipaddress' => 'root',
		'port' => '22222',
		'privatekey' => 'rsa_testserver',
	),
);

define('DATE', date('Y-m-d'));

setlocale(LC_TIME, 'bg_BG.utf8');

$currency_BGN_to_EUR = .5112918811962185;

$balance_table = 'cstrike_servers_balance';

$ftp_hostname = 'csftp.smshosting.bg';
?>

```

* server/var/www/includes/config.php

```php
<?php
// DATABASE //

define('DB_TYPE', 'mysql');
define('DB_USER', 'cstrikecp');
define('DB_PASS', 'xxxxxxxxxxxxxxx');
define('DB_HOST', 'localhost');
define('DB_NAME', 'cstrikecp');
define('DB_OPTIONS', '');

define('ROOT_DIR', '/var/www/includes/');
define('INCLUDE_LIB_DIR', ROOT_DIR . 'libs/');
define('INCLUDE_DIR', ROOT_DIR . '/');
define('SERVERS_DIR', '/home/servers/cstrike');
define('LOGS_DIR', '/home/logs/cstrike');
define('CFGS_DIR', '/usr/local/cstrike/cfgs');

define('SMTP_HOST', 'mail.example.com');
define('SMTP_PORT', 465);
define('SMTP_USER', 'mail@host.com');
define('SMTP_PASS', 'xxxxxxxxxxxxxxx');
define('SMTP_FROM', 'mail@host.com');

$username = get_current_user();
$split_id = split('-', $username);
$id = $split_id[1];
$log_file = LOGS_DIR . '/' . $id . '.log';
?>
```

* Setting and labels in includes/labels.php

## Crontabs
server/CRONTABS

You need to install all of them to be executing from your root user (or the suited user who can execute such actions according to your configuration)

## How to install the control panel
* Place all the control panel web files in the desired directory and change the settings in includes/config.php
* Import the SQL file (for example thru phpMyAdmin)
* Change your server details in includes/config.php [web] 
* Put your server SSH key in includes/ssh and set the private key names in config.php



## How to install the server
* Just place all the files according to the directories in the repository. You can change the paths as long as you change it also in the config files or scripts
* Make symlinks to the maps and valve folders (this way a lot of space is being saved when hosting multiple servers)

As example:

```
ln -s /home/servers/cstrike/maps /home/servers/cstrike/main/cstrike/maps
ln -s /home/servers/cstrike/valve /home/servers/cstrike/main/valve
```

* Make some files executable (if they are not) before runing the main server:
```
cd /home/servers/cstrike/main && chmod +x hlds_run hlds_linux hltv start stop
```

* Install the FTP server (proFTPD as example)
* Create the cstrike user group

```
groupadd cstrike
```

* Edit some scripts in /usr/local/cstrike/ for the download URL parameter

Current configuration of the main server (some of the mods/plugins needs to be updated)

```
* ReHLDS
* Metamod-r
* reUnion
* AmxModX
* ReAuthChecker
* Rechecker
* ReSemiclip
* WHBlocker
* Revoice
* ReSRDetector
* hackdetector
* ReAimDetector
```

The frontend (web) and the backend (server) dont have to be on the same physical server. The system can work with one web control panel and multiple servers and ip addresses.

* Start / Stop the server

Just execute the following commands ./start and ./stop scripts in the root main server directory. They connect with the database and get everything needed to generate the command for propper start of the server.

* Some screenshots
** Login page
![Login page](https://smshosting.bg/images/cstrikecp/login.jpg)

** Dashboard
![Dashboard](https://smshosting.bg/images/cstrikecp/dashboard.jpg)

** Mode change page
![Change mode page](https://smshosting.bg/images/cstrikecp/mode_change.jpg)

