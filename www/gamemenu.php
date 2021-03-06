<?php require_once('../templates/header.php'); ?>
<div class="container-fluid">
		<div class="content">
				<?php require_once('./includes/etc/header-stats.php');  ?>		
			<div class="row-fluid">
				<div class="span12">
							<div class="alert alert-info alert-block">
								<a href="#" data-dismiss="alert" class="close">×</a>
  								<h4 class="alert-heading">Информация!</h4>
								<b>GameMenu</b> е система която позволява на потребителя да избере дали вашия сървър да се постави като част от менюто на неговия гейм клиент.<br />
								<br /> Показва се въпрос на всеки играч който се логне в сървъра ви или може да го направи ръчно чрез командата <strong>/setmenu</strong>
							</div>
<?php if ($_SESSION['ftp'] != 2) { ?>							
							
							<?php if ($_SESSION['mode'] == 1) { ?>
							За да активирате GameMenu системата на вашия сървър трябва да имате активиран мод към вашия сървър. Това можете да направите <a href="/mode">тук</a>.
								<?php
								}
								else {
								?>							
						<?php
								if ($_SESSION['gamemenu'] == 0) { ?>							
					<div class="box">
						<div class="box-head">
							<h3>Активация на GameMenu система</h3>
						</div>
						<div class="box-content">
										<form action="/submit/gamemenu.php" method="POST">
										<input type="hidden" name="gamemenu_activate" />
										
										<br />								
										
										<div style="text-align: center;">
										<div class="error-box alert alert-block alert-danger" style="display: none;">Важно съобщение</div>
								<img src="/img/loading.gif" id="img_loading" alt="Зареждане, моля изчакайте... (Ако до две минути тази картинка не е изчезнала това означава че има някакъв проблем с услугата. Свържете се незабавно с нашия екип." />
										<input class="navigation_button btn btn-primary" type="submit" value="Активирай GameMenu" />
										</div>
										</form>
										<br />

						</div>
					</div>
								<?php 
									}
									elseif ($_SESSION['gamemenu'] == 1) {
								?>	
				<div class="box">	
							<div class="box-head tabs">
								<h3>Статус</h3>
							</div>						
						<div class="box-content box-nomargin">
<div style="text-align: center;">GameMenu системата е активна.
<br /><br />
								<form action="/submit/gamemenu_pause.php" method="POST">
								<input class="navigation_button btn btn-primary"  type="submit" value="Паузирай GameMenu" />
								<div class="error-box alert alert-block alert-danger" style="display: none;">Важно съобщение</div>
								<img src="/img/loading.gif" id="img_loading" alt="Зареждане, моля изчакайте... (Ако до две минути тази картинка не е изчезнала това означава че има някакъв проблем с услугата. Свържете се незабавно с нашия екип." />

								</form>
</div>


								
<?php } else { ?>
										Системата GameMenu е паузирана. Можете да я стартирате към вашия сървър отново.<br />
										<form action="/submit/gamemenu_unpause.php" method="POST">
										<input class="navigation_button btn btn-primary"  type="submit" value="Стартирай GameMenu" />
										<div class="error-box alert alert-block alert-danger" style="display: none;">Важно съобщение</div>
										<img src="/img/loading.gif" id="img_loading" alt="Зареждане, моля изчакайте... (Ако до две минути тази картинка не е изчезнала това означава че има някакъв проблем с услугата. Свържете се незабавно с нашия екип." />

										</form>
<?php } ?>
						</div>
				</div>			
								
								
								<?php	
									}	
								?>			
*Сървъра ще се рестартира автоматично след активация на тази опция.<br />								
*Оригиналния GameMenu.res файл който плъгина заменя можете да намерите <a href="http://downloads.smshosting.bg/GameMenu.res">тук</a>.								
				</div>
								<?php } else { ?>
				Тази функция не е налична поради пълният FTP достъп към сървъра.
			<?php } ?>				
				
			</div>
			
		</div>	
	</div>
<?php require_once('../templates/footer.php'); ?>