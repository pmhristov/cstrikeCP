<?php require_once('../templates/header.php'); ?>
<div class="container-fluid">
		<div class="content">
				<?php require_once('./includes/etc/header-stats.php');  ?>		
			<div class="row-fluid">
				<div class="span12">
					<div class="alert alert-info alert-block">
						<a href="#" data-dismiss="alert" class="close">×</a>
						<h4 class="alert-heading">Информация!</h4>
						От тази страница можете да възстановите фабричните настройките и конфигурациина вашия сървър. Това действие ще възвърне всички модове, плъгини, и античиит системии и конфигурационни файлове към фабричния им вид. Няма да премахне съдържание от вашия сървър.
					</div>				
					<div class="box">
						<div class="box-head">
							<h3>Възстановяване на настройки</h3>
						</div>
						<div class="box-content">
							<form method="POST" action="/submit/sreset.php">
								<input type="hidden" name="sreset" id="sreset" />
								<div style="text-align: center;"><input class="btn btn-danger"  type="submit" value="Възстанови конфигурация"/></div>
								<br />
								<small><span style="color: red;"><b>Прочетете информацията какво точно извършва това действие. Процеса е необратим.</b></span></small>
								<br />
								<img src="/img/loading.gif" id="img_loading" alt="Зареждане, моля изчакайте... (Ако до две минути тази картинка не е изчезнала това означава че има някакъв проблем с услугата. Свържете се незабавно с нашия екип." />
								</fieldset>
							</form>
						</div>
					</div>
				</div>
			</div>			
		</div>	
	</div>
<?php require_once('../templates/footer.php'); ?>
	