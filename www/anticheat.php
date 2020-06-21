<?php require_once('../templates/header.php'); ?>
<div class="container-fluid">
		<div class="content">
				<?php require_once('./includes/etc/header-stats.php');  ?>		
			<div class="row-fluid">
				<div class="span12">
							<div class="alert alert-info alert-block">
								<a href="#" data-dismiss="alert" class="close">×</a>
  								<h4 class="alert-heading">Информация!</h4>
  								Активирайте анти-чиит система към вашия сървър и го защитете от най - опасните хакове, фейк плеъри и други.
							</div>	
<?php if ($_SESSION['ftp'] != 2) { ?>	
<?php if ($_SESSION['mode'] != 1) { ?>
					<div class="box">
						<div class="box-head">
							<h3>Активни анти-чиит системи към сървъра</h3>
						</div>
						<div class="box-content box-nomargin">

							
							<?php
							$anticheats = $DB->GetAll('SELECT * FROM `anticheat` WHERE `server_id` = ?', $_SESSION['id']);
								if (empty($anticheats)) {
							?>
								<div style="text-align: center;"><strong>Няма активни анти-чиит системи към сървъра.</strong></div>
							<?php } else { ?>
							<table class="table table-striped table-bordered">
								<thead>
									<tr>
										<th>Дата на добавяне</th>
										<th>Античиит системата</th>
										<th></th>
									</tr>
								</thead>
								<tbody>
								
								<?php
								foreach ($anticheats as $anticheat) {
								?>
								<tr>
									<td><?php echo $anticheat['date']; ?></td>
									<td><?php echo $anticheat_labels[$anticheat['anticheat']][0]; ?></td>
								<td style="text-align: center;">
									<input type="button" <?php if ($anticheat['active'] == 1) { ?> disabled="disabled" <?php } ?> value="Активирай" name="anticheat_activate_submit" id="anticheat_activate_submit" class="navigation_button btn btn-primary" rel="<?php echo $anticheat['id']; ?>">
									<input type="button" <?php if ($anticheat['active'] == 0) { ?> disabled="disabled" <?php } ?>  value="Деактивирай" name="anticheat_deactivate_submit" class="navigation_button btn btn-primary" id="anticheat_deactivate_submit" rel="<?php echo $anticheat['id']; ?>">
								</td>									
								</tr>
								<?php } ?>
								
								</tbody>
							</table>							
							
							<?php } ?>
							
						</div>
					</div>
					<br />
					<div class="box">
						<div class="box-head">
							<h3>Добави Анти-чиит система</h3>
						</div>
						<div class="box-content">
							<form action="/submit/anticheat.php" method="post">	
								<select name="type" id="anticheat">
								<option value="" selected="selected">Изберете желаната система</option>
									<optgroup label="Сървър страна">
										<?php foreach ($anticheat_labels as $anticheat_label) {
											if ($anticheat_label[1] != '') {
										?>
											<option value="<?php echo $anticheat_label[1]; ?>"><?php echo $anticheat_label[0]; ?></option>
											<?php }} ?>
									</optgroup>
								</select>
							<br />
							<div id="anticheat_info"></div>
							<br />
							<img src="/img/loading.gif" id="img_loading" alt="Зареждане, моля изчакайте... (Ако до две минути тази картинка не е изчезнала това означава че има някакъв проблем с услугата. Свържете се незабавно с нашия екип.">	
							<div class="error-box alert alert-block alert-danger" style="display: none;">Важно съобщение</div>
								<input type="hidden" id="action" name="action" value="activate"/>
								<input class="navigation_button btn btn-primary" type="submit" value="Активирай анти-чиит система" />
							</form><br />
<strong>*Възможно е някои античиит системи да създават конфликт при работа с друга и това да попречи на работата на вашия сървър. За съжаление това няма как да бъде избегнато и ако това се получи Ви съветваме да деактивирате поне едната система.</strong>

							<?php } else { ?>
							<strong>* За да използвате функцията "Античиит" трябва да имате активиран мод към сървъра. <br />Можете да активирате мод към вашия сървър от менюто "<a href="/mode">Сървър мод</a>.</strong>"
							<?php } ?>
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
