<?php 
	require_once('../templates/header.php'); 
	$maps = $DB->GetAll('SELECT * FROM `maps`');
	$maplist = $DB->GetAll('SELECT * FROM `maplist` WHERE `server_id` = ? ORDER BY `id`', $_SESSION['id']);
	$maplist_count = $DB->GetOne('SELECT COUNT(*) FROM `maplist` WHERE `server_id` = ?', $_SESSION['id']);
?>

<div class="container-fluid">
		<div class="content">
				<?php require_once('./includes/etc/header-stats.php');  ?>		
			<div class="row-fluid">
				<div class="span12">
					<div class="alert alert-info alert-block">
						<a href="#" data-dismiss="alert" class="close">×</a>
						<h4 class="alert-heading">Информация!</h4>
						Тук можете да добавяте или премахвате карти към списък така, че само тези карти да се изреждат в самия сървър.
					</div>	
					<?php if ($_SESSION['ftp'] != 2) { ?>							

					<div class="box">
						<div class="box-head tabs">
							<h3>Списък с карти</h3>
							<ul class="nav nav-tabs">
								<li class="active">
									Добавени карти: <strong><?php echo $maplist_count; ?> </strong>
								</li>
							</ul>
						</div>	
						<div class="box-content box-nomargin">
						<?php if (!empty($maplist)) { ?>
							<table class="table table-striped table-bordered">
							<thead>
							<tr>
							<th>Карта (Map)</th>
							<th></th>
							</tr>
							</thead>
							<tbody>
							<?php foreach ($maplist as $map_val) { ?>
							<tr class="odd">
							<td><?php echo $map_val['map']; ?></td>
							<td style="text-align: center;"><input type="button" class="btn btn-danger" value="Премахни" name="maplist_remove" id="maplist_remove" rel="<?php echo $map_val['id']; ?>"></td>
							</tr>
							<?php } ?>
							</tbody>
							</table>	
						<?php } else { ?>
							<div style="text-align: center;"><strong>Няма добавени карти.</strong></div>
						<?php } ?>
						</div>
					</div>
					<div class="box">
						<div class="box-head">
							<h3>Добави карта към списъка</h3>
						</div>
						<div class="box-content">
						<form action="/submit/maplist.php" method="POST">
						<select name="map" id="map" class="cho">
							<option value="">Изберете карта</option>
							<?php
							foreach ($maps as $map) {
							?>
							<option value="<?php echo $map['map']; ?>"><?php echo $map['map']; ?></option>
							<?php
							}
							?>
						</select> 
						<p>
							<div class="error-box alert alert-block alert-danger" style="display: none;">Важно съобщение</div>
							<input class="navigation_button btn btn-primary" type="submit" class="addtocart_button_module" value="Добави карта" />
						</p>
						</form>
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