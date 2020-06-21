<?php 
	require_once('../templates/header.php'); 
	//require_once(INCLUDE_DIR . 'server_query.php');
	//$serverstatus = $rcon->exec('status');
	
	$last_login_ip = $DB->GetRow('SELECT * FROM `last` WHERE `server_id` = ? ORDER BY `id` ASC LIMIT 1', array($_SESSION['id']));
	if ($srv_details['ftp'] == 1) { $ftp = 'Частичен'; } elseif ($srv_details['ftp'] == 2) {$ftp = 'Пълен';} else {$ftp = 'Няма';}
	if ($srv_details['master'] == 1) { $master = 'Активен'; } else {$master = 'Неактивен';}
	//var_dump($nodes[$_SESSION['node_id']]);
	
?>
	<div class="container-fluid">
	
		<div class="content">
				<?php require_once('./includes/etc/header-stats.php');  ?>
			<div class="row-fluid">
				<div class="span12">
					<div class="box" style="float: left; width: 49%;">
						<div class="box-head tabs">
							<h3>Основна информация</h3>
						</div>
									<div id="condensed" class="tab-pane active">
										<table class="table table-striped table-condensed table-bordered">
											<thead>
												<tr>
													<th>Опция</th>
													<th>Стойност</th>
												</tr>
											</thead>
																						<tbody>	
												<tr>
													<td>Потребител</td>
													<td style="text-align: right;"><strong>cstrike<?php echo $_SESSION['id']; ?></strong></td>
												</tr>																						
												<tr>
													<td>Име (Hostname)</td>
													<td style="text-align: right;"><strong><?php echo $_SESSION['hostname']; ?></strong> <a href="/hostname"><img src="/img/icons/essen/16/pencil.png" alt="" /></a></td>
												</tr>												
												<tr>
													<td>Адрес на сървъра</td>
													<td style="text-align: right;"><strong><?php echo $_SESSION['ip'] . ':' . $_SESSION['port']; ?></strong></td>
												</tr>
												<tr>
													<td>Сървър план</td>
													<td style="text-align: right;"><strong><?php echo $plan_labels[$_SESSION['packageid']][0]; ?></strong></td>
												</tr>
												<tr>
													<td>FPS рейтинг</td>
													<td style="text-align: right;"><strong><?php echo $_SESSION['fps'];?> FPS</strong></td>
												</tr>													
												<tr>
													<td>Сървър мод</td>
													<td style="text-align: right;"><strong><?php echo ($srv_details['mode'] == 1 ? 'Няма активен мод': $mode_labels[$srv_details['mode']][1]) ?></strong> <a href="/mode"><img src="/img/icons/essen/16/pencil.png" alt="" /></a></td>
												</tr>														
												<tr>
													<td>Стартирваща карта</td>
													<td style="text-align: right;"><strong><?php echo $srv_details['map']; ?></strong> <a href="/map"><img src="/img/icons/essen/16/pencil.png" alt="" /></a></td>
												</tr>	
												<?php
													$last_ip = $DB->GetOne('SELECT `last_ip` FROM `last` WHERE `server_id` = ? ORDER BY `id` ASC LIMIT 1', $_SESSION['id']);
												?>
												<tr>
													<td>Последно влизал от</td>
													<td style="text-align: right;"><strong><?php echo $last_login_ip['last_ip']; ?> <a href="/lastip"><img src="/img/icons/essen/16/administrative-docs.png" alt="" style="margin-bottom: 5px;" /></a></strong></td>
												</tr>
												<tr>
													<td>Разположен на сървър</td>
													<td style="text-align: right;"><strong><?php echo $ssh_ip; ?></strong></td>
												</tr>												
											</tbody>
										</table>
									</div>
					</div>
						
						<div class="box" style="float: right; width: 49%;">
						<div class="box-head tabs">
							<h3>Състояние на сървъра</h3>
						</div>
					<ul class="quickstats" style="margin-left:10px;">	
					<?php if ($status == false || $status == 'Bad rcon_password.') { ?>
					<li>
						<div class="headicons"><img alt="" src="/img/icons/<?php echo $img; ?>.png" /></div>
						<div class="chart-detail">
							<span class="amount"><a href="#" id="server_status_button" style="color: <?php echo $color; ?>;"><?php echo $msg; ?></a></span>
							<span class="description">Сървър статус</span>
						</div>
					</li>					
					<?php } else { ?>
					
					<li>
						<div class="headicons"><img alt="" src="/img/icons/<?php echo $img; ?>.png" /></div>
						<div class="chart-detail">
							<span class="amount"><a href="#" id="server_status_button" style="color: <?php echo $color; ?>;"><?php echo $msg; ?></a></span>
							<span class="description">Сървър статус</span>
						</div>
					</li>		
					<li>
						<div class="headicons"><img alt="" src="/img/icons/player.png" /></div>
						<div class="chart-detail">
							<span class="amount"><a href="/players"><?php echo $result["activeplayers"] . ' / ' . $result["maxplayers"];?></a></span>
							<span class="description">Активни играчи</span>
						</div>
					</li>			
					<li>
					<div class="headicons"><img alt="" src="/img/icons/map.png" /></div>
						<div class="chart-detail">
							<span class="amount"><a href="#" id="map_info_button"><?php echo $result["map"]; ?></a></span>
							<span class="description">Карта</span>
						</div>
					</li>
					
					<li>
						<div class="headicons"><img alt="" src="/img/icons/plugins.png" /></div>
						<div class="chart-detail">
							<span class="amount"><a href="/plugins"><?php echo $plugins_count;?></a></span>
							<span class="description">Добавки</span>
						</div>
					</li>
					<li>
						<div class="headicons"><img alt="" src="/img/icons/crown.png" /></div>
						<div class="chart-detail">
							<span class="amount"><a href="/users"><?php echo $admins_count;?></a></span>
							<span class="description">Администратори</span>
						</div>
					</li>					
	
					<li>
					<div class="headicons"><img alt="" src="/img/icons/essen/32/networking.png" /></div>
						<div class="chart-detail">
							<span class="amount"><a href="/ftp" id="map_info_button"><?php echo $ftp; ?></a></span>
							<span class="description">FTP достъп</span>
						</div>
					</li>
					<li>
					<div class="headicons"><img alt="" src="/img/icons/essen/32/world.png" /></div>
						<div class="chart-detail">
							<span class="amount"><a href="/master" id="map_info_button"><?php echo $master; ?></a></span>
							<span class="description">Master/Internet сървър</span>
						</div>
					</li>					
					<?php } ?>							
				</ul>						

<div id="server_status_info" style="text-align: center; display: none;"><?php echo $desc; ?></div>
<div id="map_info" style="text-align: center; display: none;"><img src="/img/maps/<?php echo $map; ?>" alt="" title="" style="width: 200px; margin-bottom: 10px;"/></div>
				
					</div>								
</div>




							
						</div>
					</div>				
				</div>
			</div>
<?php require_once('../templates/footer.php'); ?>
