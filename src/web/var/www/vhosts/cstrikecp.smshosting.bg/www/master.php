<?php require_once('../templates/header.php'); ?>
<div class="container-fluid">
		<div class="content">
				<?php require_once('./includes/etc/header-stats.php');  ?>		
			<div class="row-fluid">
				<div class="span12">
					<div class="alert alert-info alert-block">
						<a href="#" data-dismiss="alert" class="close">×</a>
						<h4 class="alert-heading">Информация!</h4>
						Master сървър представлява функция, която позволява на вашия сървър да се показва в публичния интернет списък във вашия Counter-Strike гейм клиент.
					</div>				
					<div class="box">
						<div class="box-head">
							<h3>Master сървър функция</h3>
						</div>
						<div class="box-content">
							<form action="/submit/master.php" method="POST">
							<?php
								if ($_SESSION['master'] == 0) { ?>
									<br />
									<div style="text-align: center;">
									<img src="/img/loading.gif" id="img_loading" alt="Зареждане, моля изчакайте... (Ако до две минути тази картинка не е изчезнала това означава че има някакъв проблем с услугата. Свържете се незабавно с нашия екип.">			
									<div class="error-box alert alert-block alert-danger" style="display: none;">Важно съобщение</div>
									<input class="navigation_button btn btn-primary" type="submit" value="Активирай Master сървър" />
									</div>
									<br />
							<?php 
								}
								else {
							?>
							Сървъра има активирана Master сървър.<br />
							<?php	
								}	
							?>
							</form>
						</div>
					</div>
				</div>
			</div>			
		</div>	
	</div>
<?php require_once('../templates/footer.php'); ?>