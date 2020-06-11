<?php require_once('../templates/header.php'); ?>
<div class="container-fluid">
		<div class="content">
				<?php require_once('./includes/etc/header-stats.php');  ?>		
			<div class="row-fluid">
				<div class="span12">
					<div class="alert alert-info alert-block">
						<a href="#" data-dismiss="alert" class="close">×</a>
						<h4 class="alert-heading">Информация!</h4>
						Тук можете да промените вашата сървър (rcon) парола.
					</div>				
					<div class="box">
						<div class="box-head">
							<h3>Промяна на RCON парола.</h3>
						</div>
						<div class="box-content">
							<form action="/submit/rcon.php" method="POST">
							<p>
							<label for="oldrcon"><b>Сегашна RCON парола</b></label>
							<input type="password" name="oldrcon" id="oldrcon" class="text-medium"/>
							</p>
							<p>
							<label for="newrcon"><b>Нова RCON парола</b></label>
							<input type="password" name="newrcon" id="newrcon"  class="text-medium"/>
							</p>
							<p>
							<label for="renewrcon"><b>Потвърди нова RCON парола</b></label>
							<input type="password" name="renewrcon" id="renewrcon"  class="text-medium"/>
							</p>
							<p>
							
							<div class="error-box alert alert-block alert-danger" style="display: none;">Важно съобщение</div>
							<input class="navigation_button btn btn-primary" type="submit" value="Промени RCON парола" />
							</p>
							<img src="/img/loading.gif" id="img_loading" alt="Зареждане, моля изчакайте... (Ако до две минути тази картинка не е изчезнала това означава че има някакъв проблем с услугата. Свържете се незабавно с нашия екип." />									
							<strong>* При промяна на сървър паролата се променя и вашата FTP парола.</strong>
							</form>
						</div>
					</div>
				</div>
			</div>			
		</div>	
	</div>
<?php require_once('../templates/footer.php'); ?>
	