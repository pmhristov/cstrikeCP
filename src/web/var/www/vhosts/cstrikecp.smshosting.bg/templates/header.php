<?php
require_once('../includes/core.php');
if (!isset($_SESSION['id'])) {
	header('Location: ../');
}
$srv_details = $DB->GetRow('SELECT * FROM `servers` WHERE `id` = ?', array($_SESSION['id']));
$host_details = $DB->GetRow('SELECT * FROM `hosts` WHERE `server_id` = ?', array($_SESSION['id']));

if ($_SESSION['passwd'] != $srv_details['passwd']) {
	unset($_SESSION);
	session_destroy();
	header('location: ../index.php');
}

if ($srv_details['shutdown'] == 0 && !$status) {
	$login_ssh2->exec('/usr/local/cstrike/restart.sh ' . $_SESSION['id']);
	header('location: ../index.php');
}
$rqurl = explode('/', $_SERVER['REQUEST_URI']);
$rqurl = $rqurl[1];	

?>

<!doctype html>
<html lang="bg">
<head>
<meta charset="utf-8">
<title>cstrikeCP @ SMSHosting.BG</title>
<meta name="description" content="">
<META HTTP-EQUIV="Pragma" CONTENT="no-cache">
<meta name="viewport" content="width=device-width">


<link href="/img/favicon.gif" rel="shortcut icon" type="image/x-icon" />

<link rel="stylesheet" href="css/bootstrap.css">
<link rel="stylesheet" href="css/bootstrap-responsive.css">
<link rel="stylesheet" href="css/jquery.fancybox.css">
<link rel="stylesheet" href="css/uniform.default.css">
<link rel="stylesheet" href="css/bootstrap.datepicker.css">
<link rel="stylesheet" href="css/jquery.cleditor.css">
<link rel="stylesheet" href="css/jquery.plupload.queue.css">
<link rel="stylesheet" href="css/jquery.tagsinput.css">
<link rel="stylesheet" href="css/jquery.ui.plupload.css">
<link rel="stylesheet" href="css/chosen.css">
<link rel="stylesheet" href="css/jquery.jgrowl.css">
<link rel="stylesheet" href="css/style.css">
<link rel="stylesheet" href="css/colorbox.css">



</head>
<body>
<div class="topbar">
	<div class="container-fluid">
		<a href="dashboard" class='company'><img src="/img/logo-top.png" width="250" /></a>
		<ul class='mini'>	
			<li class='dropdown messageContainer'>
				<a href="#" class='dropdown-toggle' data-toggle='dropdown'>
					<img src="img/icons/fugue/gear.png" alt="">
					Сървър контрол 
				</a>
				<ul class="dropdown-menu pull-right custom custom-dark">
					<li class='custom'>
						<div class="title">
							Старт на сървъра
							<span>Използвайте ако сървъра е спрян.</span>
						</div>
						<div class="action">
							<div class="btn-group">
								<a href="#" class='tip btn btn-mini' title="Стартирай"><img id="server_start" src="/img/icons/essen/16/start.png" alt=""></a>
							</div>
						</div>
					</li>				
					<li class='custom'>
						<div class="title">
							Рестарт на рунд
							<span>Рестартирва текущия рунд след 3 секунди.</span>
						</div>
						<div class="action">
							<div class="btn-group">
								<a href="#" class='tip btn btn-mini' title="Рестартирай"><img id="restart_round" src="/img/icons/essen/16/restart.png" alt=""></a>
							</div>
						</div>
					</li>
					<li class='custom'>
						<div class="title">
							Сървър рестарт (рестарт на карта)
							<span>Рестартирва сървъра чрез командата restart .</span>
						</div>
						<div class="action">
							<div class="btn-group">
								<a href="#" class='tip btn btn-mini' title="Рестартирай"><img id="restart_map" src="/img/icons/essen/16/restart.png" alt=""></a>
							</div>
						</div>
					</li>	
					<li class='custom'>
						<div class="title">
							Системен рестарт
							<span> Спира сървъра системно след което го активира без да губите информация (нужно е играчите да се закачат отново към сървъра) .</span>
						</div>
						<div class="action">
							<div class="btn-group">
								<a href="#" class='tip btn btn-mini' title="Рестартирай"><img id="restart_system" src="/img/icons/essen/16/restart.png" alt=""></a>
							</div>
						</div>
					</li>	
					<li class='custom'>
						<div class="title">
							Стоп на сървъра
							<span>Ако желаете да стопирате вашия сървър.</span>
						</div>
						<div class="action">
							<div class="btn-group">
								<a href="#" class='tip btn btn-mini' title="Спри"><img id="server_stop" src="/img/icons/essen/16/stop.png" alt=""></a>
							</div>
						</div>
					</li>
					<li class='custom'>
						<div class="title">
							Възстановяване на настройки
							<span>Възстановяване на фабричните настройки на конфиг файловете.</span>
						</div>
						<div class="action">
							<div class="btn-group">
								<a href="/sreset" class='tip btn btn-mini' title="Възстановяване"><img id="server_stop" src="/img/icons/rebuild.png" alt=""></a>
							</div>
						</div>
					</li>					
				</ul>
			</li>
			<li>
				<a href="/log">
					<img alt="" src="/img/icons/essen/16/administrative-docs.png">
					Сървър лог
				</a>
			</li>
		
			<li>
				<a href="/includes/logout.php">
					<img src="img/icons/fugue/control-power.png" alt="">
					Изход
				</a>
			</li>
		</ul>
	</div>
</div>
<div class="breadcrumbs">
	<div class="container-fluid">
		<ul class="bread pull-left">
			<li>
				<a href="dashboard"><i class="icon-home icon-white"></i></a>
			</li>
			<?php if ($paths[$rqurl][1] == 1) { ?>
			<li>
				<a href="#">
					<?php echo $paths[$rqurl][2]; ?>
				</a>
			</li>
			<li>
				<a href="/<?php echo $rqurl; ?>">
					<?php echo $paths[$rqurl][0]; ?>
				</a>
			</li>			
			<?php } else { ?>
			<li>
				<a href="/<?php echo $rqurl; ?>">
					<?php echo $paths[$rqurl][0]; ?>
				</a>
			</li>			
			<?php } ?>
		</ul>

	</div>
</div>

<div class="main">

	<div class="navi">

		<ul class='main-nav'>

			<li <?php if (in_array($rqurl, $menus['dashboard'])) { ?> class='active' <?php } ?>>
				<a href="/dashboard" class='light toggle-collapsed'>
					<div class="ico"><i class="icon-home icon-white"></i></div>
					Основни
					<img src="img/toggle-subnav-down.png" alt="">
				</a>
				<ul class='collapsed-nav closed' <?php if (in_array($rqurl, $menus['dashboard'])) { ?> style='display: block;' <?php } ?>>
					<li>
						<a href="/dashboard">
							Основна информация
						</a>
					</li>
					<li>
						<a href="/players">
							Активни играчи
						</a>
					</li>				
					<li>
						<a href="/lastip">
							Последно влизал от
						</a>
					</li>
					<li>
						<a href="http://smshosting.bg/cstrike-renew" target="_blank">
							Подновяване на сървъра
						</a>
					</li>
					<li>
						<a href="http://smshosting.bg/knowledgebase/7/Promyana-na-plan-ili-period-na-plashtane.html" target="_blank">
							Промяна на план
						</a>
					</li>				
					<li>
						<a href="http://smshosting.bg/knowledgebase/1/Counter-Strike-sarvar" target="_blank">
							Помощна информация
						</a>
					</li>					
				</ul>
			</li>			
			<li <?php if (in_array($rqurl, $menus['basic'])) { ?> class='active' <?php } ?>>
				<a href="#" class='light toggle-collapsed'>
					<div class="ico"><i class="icon-list icon-white"></i></div>
					Контрол на сървъра
					<img src="img/toggle-subnav-down.png" alt="">
				</a>
				<ul class='collapsed-nav closed' <?php if (in_array($rqurl, $menus['basic'])) { ?> style="display: block;" <?php } ?>>
					<li>
						<a href="/map">
							Промени карта
						</a>
					</li>
					<li>
						<a href="/cmd">
							Изпрати команда
						</a>
					</li>
					<li>
						<a href="/msg">
							Изпрати съобщение
						</a>
					</li>
					<li>
						<a href="/cfgs">
							Изпълни конфигурация
						</a>
					</li>

					<li>
						<a href="/kickban">
							Изритай/Забрани играч
						</a>
					</li>	
					<li>
						<a href="/resetstats">
							Зануляване на статистики
						</a> 
					</li>					
				</ul>
			</li>	
			<li <?php if (in_array($rqurl, $menus['administration'])) { ?> class='active' <?php } ?>>
				<a href="#" class='light toggle-collapsed'>
					<div class="ico"><i class="icon-list icon-white"></i></div>
					Персонализация
					<img src="img/toggle-subnav-down.png" alt="">
				</a>
				<ul class='collapsed-nav closed' <?php if (in_array($rqurl, $menus['administration'])) { ?> style="display: block;" <?php } ?>>
					<li>
						<a href="/mode">
							Сървър мод
						</a>
					</li>
					<li>
						<a href="/plugins">
							Добавки (Плъгини)
						</a>
					</li>				
					<li>
						<a href="/users">
							Права на играчите
						</a>
					</li>
					<li>
						<a href="/anticheat">
							Анти-чиит системи
						</a>
					</li>
					<li>
						<a href="/hlxbans">
							GM AMXBans
						</a>
					</li>								
					<li>
						<a href="/ftp">
							FTP достъп
						</a>
					</li>
					<li>
						<a href="/fm">
							Файлов мениджър
						</a>
					</li>					
					<li>
						<a href="/gamemenu">
							GameMenu система
						</a>
					</li>	
					<li>
						<a href="/antihlbrute">
							Anti HLBrute система
						</a>
					</li>					
					<li>
						<a href="/master">
							Master сървър
						</a>
					</li>
					<li>
						<a href="/motd">
							MOTD
						</a>
					</li>
					<li>
						<a href="/scrollmsg">
							Скрол съобщение
						</a>
					</li>
					<li>
						<a href="/redirects">
							Пренасочвания
						</a>
					</li>
					<li>
						<a href="/maplist">
							Списък с карти
						</a>
					</li>	
					<li>
						<a href="/metamod">
							Metamod
						</a>
					</li>
					<li>
						<a href="/radio">
							Онлайн радио
						</a>
					</li>			
				</ul>				
			</li>
			<li <?php if (in_array($rqurl, $menus['settings'])) { ?> class='active' <?php } ?>>
				<a href="#" class='light toggle-collapsed'>
					<div class="ico"><i class="icon-th-large icon-white"></i></div>
					Настройки
					<img src="img/toggle-subnav-down.png" alt="">
				</a>
				<ul class='collapsed-nav closed' <?php if (in_array($rqurl, $menus['settings'])) { ?> style="display: block;" <?php } ?>>
					<li>
						<a href="/hostname">
							Име на сървър
						</a>
					</li>
					<li>
						<a href="/rcon">
							Сървър парола
						</a>
					</li>
					<li>
						<a href="/passwdcp">
							Административна парола
						</a>
					</li>					
					<li>
						<a href="/svpasswd">
							Постави/премахни svpasswd
						</a>
					</li>
				</ul>
			</li>
		</ul>
		
	</div>
