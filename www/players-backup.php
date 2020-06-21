<?php require_once('../templates/header.php'); ?>
<div class="container-fluid">
		<div class="content">
				<?php require_once('./includes/etc/header-stats.php');  ?>		
			<div class="row-fluid">
				<div class="span12">
					<div class="alert alert-info alert-block">
						<a href="#" data-dismiss="alert" class="close">×</a>
						<h4 class="alert-heading">Информация!</h4>
							От тук можете да увеличите перманентно игралните слотове на вашия сървър. Те се заплащат еднократно и важат докато не изтече сървъра.
					</div>				
					<div class="box">
						<div class="box-head">
							<h3>Увеличаване на игралните слотове</h3>
						</div>
						<div class="box-content">

								<form action="/submit/players.php" method="POST">
								<h4>Важи за два (2) игрални слота.</h4>
								<br />
								<img src="/img/icons/essen/16/bank.png" /> <span style="color: green;"><strong>Цена:</strong></span><br />
								<strong>4.80 лв с ДДС</strong>							
								<br /><br />
								<h4>Максимален брой игрални слотове който вашия план може да достигне: <?php echo $players_limit[$srv_details['plan']]; ?></h4>
								<br />
								<img src="/img/loading.gif" id="img_loading" alt="Зареждане, моля изчакайте... (Ако до две минути тази картинка не е изчезнала това означава че има някакъв проблем с услугата. Свържете се незабавно с нашия екип." />								
								<div class="error-box alert alert-block alert-danger" style="display: none;">Важно съобщение</div>
								<input class="navigation_button btn btn-primary" id="submit" type="submit" value="Увеличи слотове"/>

								</form>
						</div>
					</div>
				</div>
			</div>			
		</div>	
	</div>
<?php require_once('../templates/footer.php'); ?>
	


