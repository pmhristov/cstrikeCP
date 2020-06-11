<?php require_once('../templates/header.php'); ?>
<div class="container-fluid">
		<div class="content">
				<?php require_once('./includes/etc/header-stats.php');  ?>		
			<div class="row-fluid">
				<div class="span12">
					<div class="alert alert-info alert-block">
						<a href="#" data-dismiss="alert" class="close">×</a>
						<h4 class="alert-heading">Информация!</h4>
						Тук можете да промените името на вашия сървър.
					</div>				
					<div class="box">
						<div class="box-head">
							<h3>Промяна на име на сървъра (hostname)</h3>
						</div>
						<div class="box-content">
							<form action="/submit/hostname.php" method="POST">
							<b>Въведете ново име на сървъра (Hostname):</b><br />
							<div class="error-box alert alert-block alert-danger" style="display: none;">Важно съобщение</div>
							<input  type="text" name="newhostname" id="newhostname" /><br /><input class="navigation_button btn btn-primary" type="submit" value="Промени име" />
							</form>
						</div>
					</div>
				</div>
			</div>			
		</div>	
	</div>
<?php require_once('../templates/footer.php'); ?>
	