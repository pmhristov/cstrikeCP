<?php 
	require_once('../templates/header.php'); 
	require_once(INCLUDE_DIR . 'server_query.php');
	//$serverstatus = $rcon->exec('status');
	
	$last_login_ip = $DB->GetRow('SELECT * FROM `last` WHERE `server_id` = ? ORDER BY `id` ASC LIMIT 1', array($_SESSION['id']));
	if ($srv_details['ftp'] == 1) { $ftp = 'Частичен'; } elseif ($srv_details['ftp'] == 2) {$ftp = 'Пълен';} else {$ftp = 'Няма';}
?>
	<div class="container-fluid">
	
		<div class="content">
				<?php require_once('./includes/etc/header-stats.php');  ?>
			<div class="row-fluid">
				<div class="span12">						
</div>							
	<div class="box">
						<div class="box-head tabs">
							<h3>Играчи</h3>
						</div>

<div id="dashboardPlayersInfo" class="tab-pane active">
</div>
				
					</div>					




							
						</div>
					</div>				
				</div>
			</div>
<?php require_once('../templates/footer.php'); ?>
