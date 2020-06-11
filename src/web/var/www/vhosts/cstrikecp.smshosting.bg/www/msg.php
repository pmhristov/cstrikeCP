<?php require_once('../templates/header.php'); ?>
<div class="container-fluid">
		<div class="content">
				<?php require_once('./includes/etc/header-stats.php');  ?>		
			<div class="row-fluid">
				<div class="span12">
					<div class="alert alert-info alert-block">
						<a href="#" data-dismiss="alert" class="close">×</a>
						<h4 class="alert-heading">Информация!</h4>
						От тук можете да изпращате съобщение към самия сървър, което ще бъде видяно от всеки един играч.
					</div>				
					<div class="box">
						<div class="box-head">
							<h3>Изпрати съобщение до сървъра</h3>
						</div>
						<div class="box-content">
							<form action="/submit/msg.php" method="POST">
							<input type="text" id="msg" name="msg" style="width: 350px;"/><br />
							<div class="error-box alert alert-block alert-danger" style="display: none;">Важно съобщение</div>
							<input class="navigation_button btn btn-primary" type="submit" value="Изпрати съобщение" />
							</form>
						</div>
					</div>
				</div>
			</div>			
		</div>	
	</div>
<?php require_once('../templates/footer.php'); ?>
	