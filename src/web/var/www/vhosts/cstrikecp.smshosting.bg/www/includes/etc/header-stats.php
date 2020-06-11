<?php
	$admins_count = $DB->GetOne('SELECT COUNT(*) FROM `admins` WHERE `server_id` = ?', array($_SESSION['id']));
	$plugins_count = $DB->GetOne('SELECT COUNT(*) FROM `plugins` WHERE `server_id` = ? AND `plugin` != ?', array($_SESSION['id'], 'VIP'));
	$redirects_count = $DB->GetOne('SELECT COUNT(*) FROM `redirects` WHERE `server_id` = ?', $_SESSION['id']);
	$result['map'] = strtolower($result['map']);
	if (file_exists('img/maps/' . $result['map'] . '.jpg')) {
		$map = $result['map'] . '.jpg';
	} else {
		$map = 'unknown.png';
	}
	switch ($status) {
		case false:
			$img = 'off';
			$msg = 'Офлайн';
			$color = 'red';
			$desc = 'Вашия сървър е офлайн. Прегледайте вашия "Сървър лог" за повече информация и опитайте да го рестартирате системно.';
		break;
		case 'Bad rcon_password.':
			$img = 'warning';
			$msg = 'Грешна RCON парола.';
			$color = 'orange';		
			$desc = 'Вашия сървър функционира правилно, но RCON паролата е променена през самия сървър. Върнете RCON паролата със стойност каквато е в контролния панел или просто направете системен рестарт.';		
		break;
		default:
			$img = 'on';
			$msg = 'Онлайн';		
			$color = 'green';		
			$desc = 'Вашия сървър е напълно функциониращ и подходящ за игра.';		
		break;
	}

?>
<!--					<div class="alert alert-info alert-block">
						<a href="#" data-dismiss="alert" class="close">×</a>
						<h4 class="alert-heading">Добавяне на сървър баланс!</h4>
						Опцията за <strong><a href="/balance-add">добавяне на баланс</a></strong> е напълно активна.<br />
					</div> -->
