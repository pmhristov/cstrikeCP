<?php 
	require_once('../templates/header.php'); 
	$last_logins = $DB->GetAll('SELECT * FROM `last` WHERE `server_id` = ? AND `last_ip` != ? ORDER BY `id` DESC LIMIT 50', array($_SESSION['id'], '46.35.188.8'));
?>
<div class="container-fluid">
		<div class="content">
				<?php require_once('./includes/etc/header-stats.php');  ?>		
			<div class="row-fluid">
				<div class="span12">
							<div class="alert alert-info alert-block">
								<a href="#" data-dismiss="alert" class="close">×</a>
  								<h4 class="alert-heading">Информация!</h4>
  								От тук можете да проследите кога, от къде и кой се е логвал във вашия контролен панел.
							</div>
					<div class="box">
							<div class="box-head tabs">
								<h3>Последно влизал от ...</h3>
							</div>
						<div class="box-content box-nomargin">
<table class="table table-striped table-bordered">
<thead>
							<tr>
								<th>Време на логин</th>
								<th>IP адрес</th>
							</tr>
</thead>
<tbody>
						<?php foreach ($last_logins as $last_login) { ?>
							<tr>
								<td><?php echo $last_login['time']; ?></td>
								<td><?php echo $last_login['last_ip']; ?></td>
							</tr>

						<?php } ?>		
</tbody>
</table>					
						</div>
							
					</div>
				</div>
			</div>			
		</div>	
	</div>
<?php require_once('../templates/footer.php'); ?>
