<?php 
	require_once('../templates/header.php');
	$modules = $DB->GetAll('SELECT * FROM `modules` WHERE `server_id` = ?', array($_SESSION['id']));
?>
<div class="container-fluid">
	<div class="content">
				<?php require_once('./includes/etc/header-stats.php');  ?>		
		<div class="row-fluid">
			<div class="span12">
					<div class="alert alert-info alert-block">
						<a href="#" data-dismiss="alert" class="close">×</a>
						<h4 class="alert-heading">Информация!</h4>
						От тук можете да контролирате модулите които да бъдат активни към вашия сървър.
					</div>					
				<?php if ($_SESSION['ftp'] != 2) { ?>
				<div class="box">	
							<div class="box-head tabs">
								<h3>Модули</h3>
								<ul class="nav nav-tabs">
									<li class="active">
										 Безплатни плъгини: <strong><?php echo $plugins_free; ?></strong> | Използвани плъгини: <strong><?php echo $amx_plugins_count; ?></strong>
									</li>
								</ul>
							</div>						
						<div class="box-content box-nomargin">

						<?php
							if (empty($get_amx_plugins)) {
								echo '<div style="text-align: center;"><strong>Няма активни добавки (плъгини)</strong></div>';
							}
							else {
						?>
							<table class="table table-striped table-bordered">
								<thead>
									<tr>
										<th>Име на плъгина</th>
										<th>ID</th>
										<th></th>
									</tr>
								</thead>
								<tbody>
								
								<?php
								foreach ($get_amx_plugins as $amx_plugin) {
									$plugin_info = $DB->GetRow('SELECT * FROM `pluginlist` WHERE `name` = ?', array($amx_plugin['plugin']));
									if (!empty($plugin_info['description'])) {
										$plugin_description_string = '<span name="plugin_description" rel="' . $amx_plugin['id'] . '"><a href="#">[Описание]</a></span>';
									}
									else {
										$plugin_description_string = '<a href="' . $plugin_info['url'] . '" target="_blank">[Описание - URL]</a>';
									}
								?>
								<tr>
								<td><?php echo $plugin_info['title']; ?> <?php echo $plugin_description_string; ?></td>
								<td><?php echo $amx_plugin['plugin']; ?></td>
								<td style="text-align: center;">
									<input type="button" <?php if ($amx_plugin['active'] == 1) { ?> disabled="disabled" <?php } ?> value="Активирай" name="amx_plugin_activate_submit" id="amx_plugin_activate_submit" class="navigation_button btn btn-primary" rel="<?php echo $amx_plugin['id']; ?>">
									<input type="button" <?php if ($amx_plugin['active'] == 0) { ?> disabled="disabled" <?php } ?>  value="Деактивирай" name="amx_plugin_deactivate_submit" class="navigation_button btn btn-primary" id="amx_plugin_deactivate_submit" rel="<?php echo $amx_plugin['id']; ?>">
									<input type="button" <?php if ($amx_plugin['active'] == 0) { ?> disabled="disabled" <?php } ?>  value="Премахни" name="amx_plugin_delete_submit" class="navigation_button btn btn-danger" id="amx_plugin_delete_submit" rel="<?php echo $amx_plugin['id']; ?>">
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
							<div class="box-head tabs">
								<h3>Добави плъгин</h3>
							</div>						
						<div class="box-content">
							<?php if ($_SESSION['mode'] != 1) { ?>	
							<form action="/submit/plugins.php" method="post" >
								<div class="control-group">
									<div class="controls">
										<select name="plugin" id="plugin" class='cho' style="width: 500px;">
											<option value="" selected="selected">Изберете плъгин</option>
											<?php
												foreach ($plugins_categories as $plugin_category => $key) {
											?>
												<optgroup label="<?php echo $plugins_categories[$plugin_category]; ?>">
											<?php
											$plugins_list = $DB->GetAll('SELECT * FROM `pluginlist` WHERE `hidden` = ? AND `category` = ? ORDER BY `title` ASC', array(0, $plugin_category));
											?>	
											<?php
											foreach ($plugins_list as $plugin) {
											?>
											<option value="<?php echo $plugin['name']; ?>"><?php echo $plugin['title']; ?></option>
											<?php
											}
											?>
											</optgroup>
											<?php
											}
											?>
										</select>
	<br />
	<div id="plugin_info" name="plugin_info">
	<b>Изберете желания плъгин за да се покаже основната информация за него.</b>
	</div>
	<br />

	<div class="error-box alert alert-block alert-danger" style="display: none;">Важно съобщение</div>
	<input type="submit" class="navigation_button btn btn-primary" id="submit" value="Добави плъгин"/>	
	<br />
	<img src="/img/loading.gif" id="img_loading" alt="Зареждане, моля изчакайте... (Ако до две минути тази картинка не е изчезнала това означава че има някакъв проблем с услугата. Свържете се незабавно с нашия екип.">										
									</div>
								</div>
							</form>	<?php } else {?>
	<strong>За да използвате функцията "Плъгини" трябва да имате активиран мод към сървъра. <br />Можете да активирате мод към вашия сървър от менюто <a href="/mode">"Сървър мод"</a></strong>
	<?php } ?>
						</div>
					</div>
				</div>	
				
			</div>	
			<br />
				<div class="box">	
							<div class="box-head tabs">
								<h3>Последно добавени плъгини към списъка</h3>
							</div>						
						<div class="box-content box-nomargin">

<table class="table table-striped table-bordered">
<thead>
<tr>
<th>Име</th>
<th>Плъгин</th>
<th>Категория</th>
</tr>
</thead>
<tbody>
<?php
	foreach ($last_plugins as $last_plugin) {
?>
<tr>
	<td><?php echo $last_plugin['title']; ?></td>
	<td><?php echo $last_plugin['name']; ?></td>
	<td><?php echo $last_plugin['category']; ?></td>
</tr>
<?php
	}
?>
</tbody>
</table>


						</div>
						
				</div>	
	<strong>*Ако имате заявка за плъгин, моля изпратете ни официалния адрес към него на адрес <a href="mailto:office@smshosting.bg">office@smshosting.bg</a></strong>
	<br />
	<strong>*СМСХостинг.БГ НЕ отговаря за неработещи плъгини заявени от потребители.</strong>	<br />			
	<strong>*СМСХостинг.БГ НЕ отговаря за настройката на отделните плъгини. За тази цел е предоставено описание към всеки плъгин по което да се ръководите.</strong>
			<?php } else { ?>
				Тази функция не е налична поради пълният FTP достъп към сървъра.
			<?php } ?>		
		</div>
	</div>
</div>
<?php require_once('../templates/footer.php'); ?>
