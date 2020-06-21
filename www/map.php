<?php 
	require_once('../templates/header.php'); 
	$maps_list = $DB->GetAll('SELECT * FROM `maps` WHERE `hidden` = ?', array(0));
?>
<div class="container-fluid">
		<div class="content">
				<?php require_once('./includes/etc/header-stats.php');  ?>		
			<div class="row-fluid">
				<div class="span12">
					<div class="alert alert-info alert-block">
						<a href="#" data-dismiss="alert" class="close">×</a>
						<h4 class="alert-heading">Информация!</h4>
						От тук можете да промените картата на вашия сървър.
					</div>				
					<div class="box">
						<div class="box-head">
							<h3>Промяна на карта</h3>
						</div>
						<div class="box-content">
							<form action="/submit/map.php">
							<select id="map" name="map" class='cho' style="width: 270px;">
							<option value="" selected="selected">Изберете карта</option>
							<?php 
							
							foreach ($maps_list as $map_list) { ?>
								<option value="<?php echo $map_list['map']; ?>"><?php echo $map_list['map']; ?></option>
							<?php
							}
							?>
							</select>
							<br /><br />
							<div class="error-box alert alert-block alert-danger" style="display: none;">Важно съобщение</div>
							<input type="checkbox" name="map_default"/> Направи картата основна за сървъра (Изисква рестарт)<br /><br />
							<img src="/img/loading.gif" id="img_loading" alt="Зареждане, моля изчакайте... (Ако до две минути тази картинка не е изчезнала това означава че има някакъв проблем с услугата. Свържете се незабавно с нашия екип." />
							<input class="navigation_button btn btn-primary" type="submit" value="Промени карта" />
							</form>
						</div>
					</div>
				</div>
			</div>			
		</div>	
	</div>
<?php require_once('../templates/footer.php'); ?>
	