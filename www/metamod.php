<?php 
	require_once('../templates/header.php'); 
	$result["meta"] = trim(array_pop(explode(' ', $server->RconCommand("meta list"))), '"');
?>

<div class="container-fluid">
		<div class="content">
				<?php require_once('./includes/etc/header-stats.php');  ?>		
			<div class="row-fluid">
				<div class="span12">
					<div class="alert alert-info alert-block">
						<a href="#" data-dismiss="alert" class="close">×</a>
						<h4 class="alert-heading">Информация!</h4>
						От тук можете да активирате или деактивирате Metamod добавката към вашия сървър.
					</div>	
<?php if ($_SESSION['ftp'] != 2) { ?>					
					<div class="box">
						<div class="box-head">
							<h3>Metamod</h3>
						</div>
						<div class="box-content">
							<form action="/submit/metamod.php" method="POST">
							<p>
							<?php 
							if ( $_SESSION['metamod'] == 1 ) {
							?>
							Статус: <strong><font color="blue">Активен</font></strong>
							</p>
							<p>
							<input type="hidden" name="metamod_deactivate" value="1" />
							<input class="btn btn-danger" type="submit" value="Деактивирай">
							</p>
							<?php 
							} 
							else {
							?>
							Статус: <strong><font color="red">Неактивен</font></strong>
							</p>
							<p>
							<input type="hidden" name="metamod_activate" value="1" />
							<input class="navigation_button btn btn-primary" type="submit" value="Активирай">
							</p>
							<?php
							}
							?>
							</form>
							<p>
							*** <strong>Активен (Препоръчително)</strong> - Всички модове с които сте поръчали сървъра или в последствие сте добавили са на лице както и мода за 47/48 протокол
							</p>
							<p> 
							*** <strong>Неактивен</strong> - Премахва всички модове на сървъра включително и мода с който могат да влизат 47/48 протокол.
							</p>

						</div>
					</div>
													<?php } else { ?>
				Тази функция не е налична поради пълният FTP достъп към сървъра.
			<?php } ?>
				</div>
			</div>			
		</div>	
	</div>
<?php require_once('../templates/footer.php'); ?>
