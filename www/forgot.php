<?php
require_once('../includes/config.php');
require_once(INCLUDE_DIR . 'db.php'); // db connection
require_once(INCLUDE_DIR . 'labels.php'); // labels
require_once(INCLUDE_DIR . 'functions.php'); // common functions

if ( !empty($_SESSION['id']) ) {
	header('location: ../dashboard');
}

if (!empty($_POST)) {
	if (!empty($_POST['username']) && !empty($_POST['mail'])) {
		$username = trim($_POST['username']);
		$server_id = explode('cstrike', $username);
		$server_id = $server_id[1];
		$mail = trim($_POST['mail']);

		$server_data = $DB->GetRow('SELECT * FROM  `servers` WHERE `active` = ? AND `id` = ? AND `mail` = ?', array(1, $server_id, $mail));
		if (!empty($server_data)) {
			if ($server_data['mail'] !== '') {
				$passwd = random_gen(10);
				$DB->Execute('UPDATE `servers` SET `passwd_cp` = ? WHERE `id` = ?', array($passwd, $server_data['id']));
			send_email(
					$server_data['mail'],
					'Counter-Strike сървър - Забравена парола',
	'
	<fieldset>
	<legend>Логин данни</legend>
	Потребител: <b>cstrike' . htmlspecialchars($server_data['id']) . '</b><br />
	Нова парола: ' . htmlspecialchars($passwd) . '<br />
	</fieldset>	
	', true);
			}
			
			$succ_msg = 'Логин данните са изпратени на посочения от вас адрес.';
		}
		else {
			$error = 'Грешен мейл адрес';
		}
	}
	else {
		$error = 'Попълнете всички полета.';
	}
}
?>

<?php require_once('../templates/header-login.php'); ?>
<body class='login_body'>
	<div class="wrap">
		<div style="text-align: center"><img alt="" src="/img/logo.png" /></div>


		<form action=""  autocomplete="off" method="post">
		<div class="login">
			<div class="email">
				<label for="user">Потребител</label><div class="email-input"><div class="input-prepend"><span class="add-on"><i class="icon-user"></i></span><input type="text" id="user" name="username"></div></div>
			</div>
			<div class="pw">
				<label for="pw">Мейл адрес</label><div class="pw-input"><div class="input-prepend"><span class="add-on"><i class="icon-envelope"></i></span><input type="text" id="pw" name="mail"></div></div>
			</div>
		</div>
		<div class="submit">
			<a href="/">Логин страница</a>
			<button class="btn btn-red5">Изпрати</button>
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