<?php
require_once('../includes/config.php');
require_once(INCLUDE_DIR . 'db.php'); // db connection
require_once(INCLUDE_DIR . 'labels.php'); // labels
require_once(INCLUDE_DIR . 'functions.php'); // common functions

if ( !empty($_SESSION['id']) ) {
	header('location: /dashboard');
}

if (!empty($_POST)) {
	$username = trim($_POST['username']);
	$server_id = explode('cstrike', $username);
	$server_id = $server_id[1];
	$rcon = trim($_POST['rcon']);
	var_dump($rcon);
	if (!empty($username) && !empty($rcon)) {

		$server_data = $DB->GetRow('SELECT * FROM `servers` WHERE `active` = ? AND `id` = ? AND `passwd_cp` = ?', array(1, $server_id, $rcon));
		$host_data = $DB->GetRow('SELECT * FROM `hosts` WHERE `server_id` = ?', array($server_id));
		var_dump($server_data);
		if (!empty($server_data)) {
			if ($server_data['suspended'] == 0) {
				$columns = $DB->Execute('SHOW COLUMNS FROM `servers`');
				foreach ($columns as $column) {
					$_SESSION[$column['Field']] = $server_data[$column['Field']];
				}
				$_SESSION['ip'] = $host_data['ip'];
				$_SESSION['port'] = $host_data['port'];
				$_SESSION['node_id'] = $DB->GetOne('SELECT `node_id` FROM `hosts` WHERE `id` = ?', array($host_data['id']));;
				$_SESSION['client_ip'] = $_SERVER['REMOTE_ADDR'];

				$DB->Execute('INSERT INTO `last` SET `server_id` = ?, `last_ip` = ?', array($_SESSION['id'], $_SESSION['client_ip']));
				header('location: ../dashboard');
			}
			else {
				$error = 'Вашия сървър е деактивиран. <br /><b>Данните ви са запазени</b>. <br />Ако смятате, че вашия сървър е изтекъл то тогава можете да го подновите от вашата <a href="http://www.smshosting.bg/clientarea.php" target="_blank">клиентска част</a>. Ако сървъра не бъде подновен е възможно да бъде премахнат изцяло от системата. Ако нещо Ви затруднява можете да се свържете с нас на един от посочените контакти <a href="http://www.smshosting.bg/support.html" target="_blank">контакти</a>';
			}
		}
		else {
			$error = 'Грешни потребител или парола';
		}
	}
	else {
		$error = 'Грешка: попълнете всички полета.';
	}
}
?>
<?php require_once('../templates/header-login.php'); ?>
<body class='login_body'>
	<div class="wrap">
<div style="text-align: center"><img alt="" src="/img/logo.png" /></div>
<div style="text-align: center;"><strong>Вход в системата</strong></div>

		<form action=""  autocomplete="off" method="post">
		<div class="login">
			<div class="email">
				<label for="user">Потребител</label><div class="email-input"><div class="input-prepend"><span class="add-on"><i class="icon-user"></i></span><input type="text" id="user" name="username"></div></div>
			</div>
			<div class="pw">
				<label for="pw">Парола</label><div class="pw-input"><div class="input-prepend"><span class="add-on"><i class="icon-lock"></i></span><input type="password" id="pw" name="rcon"></div></div>
			</div>
			<div style="text-align: center;"><a href="/forgot">Забравена парола?</a></div>
		</div>
		<div class="submit">
			 <a href="/sysres">Системен рестарт</a> <a href="/sreslogin">Фабрични настройки</a> 
			<button class="btn btn-red5">Вход</button>
		</div>
		<?php if (isset($error)) { ?>
		<div class="alert alert-block alert-danger">
		  <?php echo $error; ?>
		</div>
		<?php } ?>
		</form>
	</div>
<script src="js/jquery.js"></script>
</body> 
<?php require_once('../templates/footer-login.php'); ?>   