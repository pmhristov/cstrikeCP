<?php require_once('../templates/header.php'); ?>
<div class="container-fluid">
		<div class="content">
				<?php require_once('./includes/etc/header-stats.php');  ?>		
			<div class="row-fluid">
				<div class="span12">
					<div class="alert alert-info alert-block">
						<a href="#" data-dismiss="alert" class="close">×</a>
						<h4 class="alert-heading">Информация!</h4>
						Чрез тази опция бързо и лесно можете да настройте вашия сървър за отборна игра, 1v1, AWP и други
					</div>				
					<div class="box">
						<div class="box-head">
							<h3>Промяна на сървър мод</h3>
						</div>
						<div class="box-content">
	
							<form action="/submit/cfgs.php" method="POST">
							<p>
							<select name="cfg" id="cfg">
							<option value="" selected="selected">Изберете конфигурация</option>
							<optgroup label="Класифицирани настроики">
							<option value="knife">Knife</option>
							</optgroup>
							<optgroup label="Други">
							<option value="warmup">WarmUP</option>
							<option value="start">Start | Live</option>
							</optgroup>
							</select>
							</p>
							<!-- <p>
							<input type="checkbox" name="cfg_save" value="1" /> Постави тази конфигурация като фабрична за моя сървър.
							</p> -->
							<p>
							<input class="navigation_button btn btn-primary" type="submit" value="Изпълни конфигурация" />
							</p>
							</form>
						</div>
					</div>
				</div>
			</div>			
		</div>	
	</div>
<?php require_once('../templates/footer.php'); ?>