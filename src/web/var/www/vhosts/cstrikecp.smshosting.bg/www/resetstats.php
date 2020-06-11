<?php require_once('../templates/header.php'); ?>
<div class="container-fluid">
		<div class="content">
				<?php require_once('./includes/etc/header-stats.php');  ?>		
			<div class="row-fluid">
				<div class="span12">
							<div class="alert alert-info alert-block">
								<a href="#" data-dismiss="alert" class="close">×</a>
  								<h4 class="alert-heading">Информация!</h4>
  																От тук можете да рестартирате статистиките в сървъра от рода на /top15 /top /me и т.н.
							</div>				
					<div class="box">
						<div class="box-head">
							<h3>Зануляване на статистики</h3>
						</div>
						<div class="box-content">

							
								<?php if ($_SESSION['mode'] == 1) { ?>

								За да използвате тази функция трябва да имате активен мод към вашия сървър. Това можете да направите <a href="/mode">тук</a>.
								<?php
								}
								else {
								?>

								<form action="/submit/resetstats_soft.php" method="POST">
								<input class="navigation_button btn btn-primary"  type="submit" value="Занули статистики" /><br />
								<strong>*Зануляват се единствено базовите статистики на сървъра (не изисква рестарт).</strong>
								</form>
								
								<form action="/submit/resetstats_hard.php" method="POST">
								<input class="navigation_button btn btn-primary"  type="submit" value="Премахни файлове" /><br />
								<strong>*Изтриват се всички файлове от директорията в която се записват всички статистики като основни, такива към модове и плъгини и други. <br />Използвайте това в случай, че първия метод не проработи. <br />Сървъра се рестартира.</strong>
								</form>

								<div class="error-box alert alert-block alert-danger" style="display: none;">Важно съобщение</div>
								<img src="/img/loading.gif" id="img_loading" alt="Зареждане, моля изчакайте... (Ако до две минути тази картинка не е изчезнала това означава че има някакъв проблем с услугата. Свържете се незабавно с нашия екип." />								
								<?php	
								}
								?>
						</div>
					</div>
				</div>
			</div>			
		</div>	
	</div>
<?php require_once('../templates/footer.php'); ?>