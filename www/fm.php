<?php 
require_once('./includes/fm/class/FileManager.php');
require_once('../templates/header.php'); 
?>

<div class="container-fluid">
		<div class="content">
			<div class="row-fluid">
				<div class="span12">
							<div class="alert alert-info alert-block">
								<a href="#" data-dismiss="alert" class="close">×</a>
  								<h4 class="alert-heading">Информация!</h4>
								<b>Файлов мениджър</b> е система която ви позволява редактирането на конфигурационните файлове при активиран FTP достъп без да е нужно да използвате какъвто и да е FTP клиент.
							</div>										
					<div class="box">
						<div class="box-head">
							<h3>Файлов мениджър</h3>
						</div>
						<div class="box-content">
						<?php if ($_SESSION['ftp'] > 0) { ?>
						<?php 
	$FileManager = new FileManager();  
	$FileManager->ftpHost = $_SESSION['ip'];
	$FileManager->ftpUser = 'cs' . $_SESSION['id'];
	$FileManager->ftpPassword = $_SESSION['passwd_cp'];
	$FileManager->ftpPort = 21;
	$FileManager->ftpPassiveMode = false;
	$FileManager->ftpSSL = false;
	$FileManager->fmView = 'icons';
	$FileManager->tmpFilePath = '/var/www/vhosts/cstrikecp.smshosting.bg/www/includes/fm/tmp/';
	$FileManager->fmWidth = 1000;
	$FileManager->fmHeight = 600;
	$FileManager->explorerWidth = 0;
	$FileManager->encoding = 'UTF-8';
	$FileManager->language = 'bg';
	$FileManager->hideDisabledIcons = 'yes';
	$FileManager->hideColumns = array('owner', 'group', 'permissions');
	$FileManager->hideSystemType = true;
	$FileManager->enableNewDir = 'no';
	$FileManager->enableUpload = 'no';
	$FileManager->enableDelete = 'no';
	$FileManager->enableRestore = 'no';
	$FileManager->enablePermissions = 'no';
	$FileManager->enableRename = 'no';
	$FileManager->enableMove = 'no';
	print $FileManager->create();
							?>
							<ul>
							<li>За да добавите определена команда към вашият сървър която да се изпълнява при рестарт или смяна на карта е нужно да я добавите към файла personal.cfg</li>
							</ul>
						<?php } else { ?>
							<strong>За да използвате функцията "Файлов мениджър" трябва да имате активиран FTP достъп към сървъра. <br />Можете да го активирате от менюто <a href="/ftp">"FTP достъп"</a></strong>
						<?php } ?>
					</div>
					</div>				
				</div>
			</div>			
		</div>	
	</div>
<?php require_once('../templates/footer.php'); ?>