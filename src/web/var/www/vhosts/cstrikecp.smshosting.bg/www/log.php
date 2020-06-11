<?php require_once('../templates/header.php'); ?>
<div class="container-fluid">
		<div class="content">
				<?php require_once('./includes/etc/header-stats.php');  ?>		
			<div class="row-fluid">
				<div class="span12">
					<div class="alert alert-info alert-block">
						<a href="#" data-dismiss="alert" class="close">×</a>
						<h4 class="alert-heading">Информация!</h4>
						Тук можете да разгледате системния лог на вашия сървър.
					</div>				
					<div class="box">
						<div class="box-head">
							<h3>Промяна на сървър мод</h3>
						</div>
						<div class="box-content">
						<form action="/submit/log.php" method="post">
							<div style="text-align: center;"><input class="navigation_button btn btn-primary" type="submit" value="Изчисти лога" /><br />
							<small>(Лога се изчиства автоматично на всеки 24 часа.)<br />Времето за зареждане на лог файла може да бъде различно в зависимост от големината на този лог файл.<br />
							====================================================</small>
							</div>
							
						</form>
							
							<?php 
							
							echo '<pre>' . $login_sftp->get('/home/logs/cstrike/' . $_SESSION['id'] . '.log') . '</pre>';
							?>
						</div>
					</div>
				</div>
			</div>			
		</div>	
	</div>
<?php require_once('../templates/footer.php'); ?>
	