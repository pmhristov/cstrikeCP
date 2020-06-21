<?php
require_once('../includes/config.php');
require_once(INCLUDE_DIR . 'db.php'); // db connection
require_once(INCLUDE_DIR . 'labels.php'); // labels
require_once(INCLUDE_DIR . 'functions.php'); // common functions

if ( !empty($_SESSION['id']) ) {
	header('location: ../dashboard');
}

if (!empty($_POST)) {
	$username = trim($_POST['username']);
	$server_id = explode('cstrike', $username);
	$server_id = $server_id[1];
	$rcon = trim($_POST['rcon']);
	
	if (!empty($username) && !empty($rcon)) {

		$server_data = $DB->GetRow('SELECT * FROM `servers` WHERE `active` = ? AND `id` = ? AND `passwd` = ?', array(1, $server_id, $rcon));

		if (!empty($server_data)) {
			if ($server_data['suspended'] == 0) {
				if ($server_data['id'] >= 1 && $server_data['id'] < 191) {
					$ssh_ip = 'lois.novahost.bg';
				}
				elseif ($server_data['id'] >= 191) {
					$ssh_ip = 'cleveland.novahost.bg';
				}
				$key = new Crypt_RSA();
				$key->loadKey(file_get_contents('/var/www/.ssh/id_rsa_hlds'));
				
				$login_sftp = new Net_SFTP($ssh_ip, 22222);
				$login_sftp->login('root', $key);
				
				$login_ssh2 = new Net_SSH2($ssh_ip, 22222);
				$login_ssh2->login('root', $key);	
		
				$login_ssh2->exec('/usr/local/cstrike/restart.sh ' . $server_data['id']);
				$succ_msg = 'Сървъра е успешно рестартиран.';
			}
			else {
				$error = 'Сървъра е деактивиран поради просрочена дата за подновяване. <br /><b>Данните ви са запазени</b>. <br />Ако желаете да подновите вашия сървър можете да го направите от <a href="http://www.smshosting.bg/cstrike-renew" target="_blank">тук</a>. Сървъра ще бъде премахнат напълно от системата до 2 дни след датата на изтичане - ' . fixDate(dateAdd($server_data['expire'], false, 2), true);
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
<div style="text-align: center;"><strong>Системен рестарт</strong></div>

		<form action=""  autocomplete="off" method="post">
		<div class="login">
			<div class="email">
				<label for="user">Потребител</label><div class="email-input"><div class="input-prepend"><span class="add-on"><i class="icon-user"></i></span><input type="text" id="user" name="username"></div></div>
			</div>
			<div class="pw">
				<label for="pw">Парола</label><div class="pw-input"><div class="input-prepend"><span class="add-on"><i class="icon-lock"></i></span><input type="password" id="pw" name="rcon"></div></div>
			</div>
		</div>
		<div class="submit">
			 <a href="/">Логин страница</a> <a href="/forgot">Забравена парола?</a>
			<button class="btn btn-red5">Изпълни</button>
		</div>
		<?php if (isset($error)) { ?>
		<div class="alert alert-block alert-danger">
		  <?php echo $error; ?>
		</div>
		<?php } ?>
		<?php if (isset($succ_msg)) { ?>
		<div class="alert alert-block alert-success">
		  <?php echo $succ_msg; ?>
		</div>
		<?php } ?>			
		</form>
	</div>
<script src="js/jquery.js"></script>
</body> 
<?php require_once('../templates/footer-login.php'); ?>   