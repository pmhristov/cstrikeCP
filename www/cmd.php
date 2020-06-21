<?php require_once('../templates/header.php'); ?>
<div class="container-fluid">
		<div class="content">
				<?php require_once('./includes/etc/header-stats.php');  ?>		
			<div class="row-fluid">
				<div class="span12">
					<div class="alert alert-info alert-block">
						<a href="#" data-dismiss="alert" class="close">×</a>
						<h4 class="alert-heading">Информация!</h4>
						От тук можете да изпращате RCON команди към сървъра, които той да изпълни.
					</div>				
					<div class="box">
						<div class="box-head">
							<h3>Изпрати команда към сървъра</h3>
						</div>
						<div class="box-content">
							<form action="/submit/cmd.php" method="POST">
							<input type="text" style="width: 300px;" id="server_cmd" name="server_cmd" /><br /> 
							<div class="error-box alert alert-block alert-danger" style="display: none;">Важно съобщение</div>
							<input class="navigation_button btn btn-primary" type="submit" class="addtocart_button_module" value="Изпрати команда" />
							</form><br />
							<a href="http://help.smshosting.bg/%D0%BE%D1%81%D0%BD%D0%BE%D0%B2%D0%BD%D0%B8-%D1%81%D1%8A%D1%80%D0%B2%D1%8A%D1%80%D0%BD%D0%B8-%D0%BA%D0%BE%D0%BC%D0%B0%D0%BD%D0%B4%D0%B8/" target="_blank">Основни сървърни команди</a>
						</div>
							<ul>
							<li>За да добавите определена команда към вашият сървър която да се изпълнява при рестарт или смяна на карта е нужно да я добавите към файла personal.cfg</li>
							</ul>
					</div>
				</div>
			</div>			
		</div>	
	</div>
<?php require_once('../templates/footer.php'); ?>
	