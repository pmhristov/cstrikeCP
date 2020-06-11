<?php require_once('../templates/header.php'); ?>
<div class="container-fluid">
		<div class="content">
				<?php require_once('./includes/etc/header-stats.php');  ?>		
			<div class="row-fluid">
				<div class="span12">
					<div class="alert alert-info alert-block">
						<a href="#" data-dismiss="alert" class="close">×</a>
						<h4 class="alert-heading">Информация!</h4>
						От тук можете да научите вашите данни за FTP достъп където можете да редактирате конфигурационните файлове на вашия мод по ваш избор. FTP достъп се активира еднократно.
					</div>				
					<div class="box">
						<div class="box-head">
							<h3>FTP достъп</h3>
						</div>
						<div class="box-content">
								Вашите данни за FTP достъп:<br /><br />
								<p>
								Хост: <b><?php echo $_SESSION['ip']; ?></b><br />
								Порт: <b>21</b><br />
								Потребител: <b>cs<?php echo $_SESSION['id']; ?></b><br />
								Парола: <strong><вашата административна парола></strong><br />
								<p>
								Достъп: <?php if ($_SESSION['ftp'] == 1) { ?> <strong>Стандартен</strong> <?php } ?> <?php if ($_SESSION['ftp'] == 2) { ?> <strong>Пълен</strong> <?php } ?>
								</p>
							
								<form action="/submit/ftp.php" method="POST">
								
								<?php if($srv_details['ftp'] == 1) { ?>
									<br />

								<div class="error-box alert alert-block alert-danger" style="display: none;">Важно съобщение</div>
								<input type="hidden" name="ftp_fullaccess" />
								<div style="text-align: center;"><input class="navigation_button btn btn-primary" type="submit" value="Активирай ПЪЛЕН FTP достъп" /></div>
								<br />
								<?php } else { ?>
								<input type="hidden" name="ftp_semiaccess" />
								<div style="text-align: center;"><input class="navigation_button btn btn-primary" type="submit" value="Активирай Стандартен FTP достъп" /></div>
								<?php } ?>
								

								<p>
									<span style="color: red;"><strong>Стандартния FTP достъп</strong></span> ограничава достъп до някои файлове и директории тъй като те се генерират автоматично от контролния панел. Стандартния ФТП достъп Ви позволява използването на автоматизирани функции създадени от нас за да Ви улеснят.<br />
									<span style="color: red;"><strong>Пълния FTP достъп</strong></span> позволява пълен достъп до всеки файл и всяка една директория, но деактивира стандартните функции на контролния панел тъй като тези файлове вече могат да бъдат презаписани от вас. Ако не сте наясно какво точно правите Ви препоръчваме да използвате стандартния достъп. <span style="color: red;"><strong>*Промените които правите по конфигурационните файлове са изцяло на ваша отговорност.</strong></span>
								</p>
								<p>
									Ако по някакъв начин объркате или поставите грешна конфигурация на вашия сървър и не може да стартира, можете да използвате функцията за <a href="/sreset" target="_blank"><strong>възстановяване на настройки</strong></a> в контролния панел или същата функция в логин страницата (ако контролния панел не зарежда поради забил процес на вашия сървър).
								</p>
								<p>
								Повече информация относно как да се свържете с FTP можете да прочетете <a href="https://smshosting.bg/knowledgebase/4/Kakvo-predstavlyava-FTP-i-kak-da-rabotim-s-nego-.htmlz`" target="_blank">тук</a><br />
								Можете да използвате фунцкията "<a href="/fm">Файлов мениджър</a>" за по - лесен достъп.
								</p>

								
								<strong>*Ако желаете да добавяте перманентни команди към вашия сървър можете да го направите към файла personal.cfg в главната ви cstrike директория. <span style="color: red;"><strong>*Уверете се че въвеждате правилни команди в противен случай сървъра може да не стартира.</strong></span></strong>
								</form>
						</div>
					</div>
				</div>
			</div>			
		</div>	
	</div>
<?php require_once('../templates/footer.php'); ?>
	


