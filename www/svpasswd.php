<?php 
	require_once('../templates/header.php'); 
	$result["svpasswd"] = trim(array_pop(explode(' ', $server->RconCommand("sv_password"))), '"');	
?>
<div class="container-fluid">
		<div class="content">
				<?php require_once('./includes/etc/header-stats.php');  ?>		
			<div class="row-fluid">
				<div class="span12">
					<div class="alert alert-info alert-block">
						<a href="#" data-dismiss="alert" class="close">×</a>
						<h4 class="alert-heading">Информация!</h4>
						Тук променяте или премахвате вашата сървър парола (sv_password)
					</div>				
					<div class="box">
						<div class="box-head">
							<h3>Сървър парола (svpasswd)</h3>
						</div>
						<div class="box-content">
							<strong>Парола в момента</strong>: <?php
							if ( $result['svpasswd'] !== "" ) {
							echo '<font color="red">' . strtolower($result['svpasswd']) . '</font>'; 
							}
							else {
							echo '<font color="red">НЯМА</font>'; 
							}
							?></b><br /><br />
													<fieldset>
							<form action="/submit/svpasswd.php" method="POST">
							<p>
							<label for="cfg"><b>Промени сървър парола на:</b></label> 
							<input type="text" name="svpasswd"  class="text-medium" />
							</p>
							<p>
							<div class="error-box alert alert-block alert-danger" style="display: none;">Важно съобщение</div>
							<input class="navigation_button btn btn-primary" type="submit" value="Промени сървър парола" />
							</p>
							</form>
							* За да премахнете сървър паролата си изпрате команда към сървъра <i>sv_password ""</i>
						</div>
					</div>
				</div>
			</div>			
		</div>	
	</div>
<?php require_once('../templates/footer.php'); ?>
	