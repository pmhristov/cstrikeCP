<?php require_once('../templates/header.php'); ?>
<div class="container-fluid">
		<div class="content">
				<?php require_once('./includes/etc/header-stats.php');  ?>		
			<div class="row-fluid">
				<div class="span12">
					<div class="alert alert-info alert-block">
						<a href="#" data-dismiss="alert" class="close">×</a>
						<h4 class="alert-heading">Информация!</h4>
						От тук можете да подновите вашия сървър чрез баланса към вашия акаунт.
					</div>				
					<div class="box">
						<div class="box-head">
							<h3>Подновяване на сървър</h3>
						</div>
						<div class="box-content">

							
								<form action="/submit/renew.php" method="POST">
								<?php if ($_SESSION['mode'] == 1) { ?>
								За да активирате ФТП достъп до конфигурационната папка на вашия сървър трябва да имате активиран мод. Това можете да направите <a href="/mode">тук</a>.
								<?php
								}
								else {
								?>
								
								<?php if($srv_details['ftp'] == 0) { ?>
									<br />
									<img src="/img/icons/essen/16/bank.png" /> <span style="color: green;"><strong>Цена:</strong></span><br />
									<strong>2.40 лв с ДДС</strong>							
									<br />

								<div class="error-box alert alert-block alert-danger" style="display: none;">Важно съобщение</div>
								<div style="text-align: center;"><input class="navigation_button btn btn-primary" type="submit" value="Активирай FTP достъп" /></div>
								<br />
								<?php } else { ?>

								Вашите данни за FTP достъп:<br /><br />
								<p>
								Хост: <b><?php echo $ftp_hostname; ?></b><br />
								Порт: <b>21</b><br />
								Потребител: <b>cs<?php echo $_SESSION['id']; ?></b><br />
								Парола: <strong><вашата сървър парола></strong><br />
								<p>
								Повече информация относно как да се свържете с FTP можете да прочетете <a href="http://help.smshosting.bg/%D0%BA%D0%B0%D0%BA%D0%B2%D0%BE-%D0%BF%D1%80%D0%B5%D0%B4%D1%81%D1%82%D0%B0%D0%B2%D0%BB%D1%8F%D0%B2%D0%B0-ftp-%D0%B8-%D0%BA%D0%B0%D0%BA-%D0%B4%D0%B0-%D1%80%D0%B0%D0%B1%D0%BE%D1%82%D0%B8%D0%BC-%D1%81/" target="_blank">тук</a><br />
								Можете да използвате фунцкията "<a href="/fm">Файлов мениджър</a>" за по - лесен достъп.
								</p>
								<?php } ?>
								<?php } ?>

								<span style="color: red;"><strong>*Промените които правите по конфигурационните файлове са изцяло на ваша отговорност.</strong></span><br />
								<span style="color: red;"><strong>*Уверете се че въвеждате правилни команди в противен случай сървъра може да не стартира.</strong></span><br />
								<strong>С цел сигурност някои от директориите не са достъпни.</strong><br />
								<strong>Директорията maps не е достъпна тъй като всички карти важат за всеки един сървър. Ако желаете определени карти можете да ни ги изпратите като заявка на един от посочените <a href="http://www.smshosting.bg/contacts" target="_blank">контакти</a> в сайта..</strong><br />
								<strong>*Ако желаете да добавяте перманентни команди към вашия сървър можете да го направите към файла personal.cfg в главната ви cstrike директория.</strong>
								</form>
						</div>
					</div>
				</div>
			</div>			
		</div>	
	</div>
<?php require_once('../templates/footer.php'); ?>
	


