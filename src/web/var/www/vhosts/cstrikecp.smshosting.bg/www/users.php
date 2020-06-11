<?php 
	require_once('../templates/header.php'); 
	//$DB->debug=true;
	$server_data = $DB->GetRow('SELECT * FROM `servers` WHERE `id` = ?', array($_SESSION['id']));
	$admins = $DB->GetAll('SELECT * FROM `admins` WHERE `server_id` = ?', $_SESSION['id']);
	$admins_count = $DB->GetOne('SELECT COUNT(*) FROM `admins` WHERE `server_id` = ?', $_SESSION['id']);
?>
<div class="container-fluid">
		<div class="content">
				<?php require_once('./includes/etc/header-stats.php');  ?>		
			<div class="row-fluid">
				<div class="span12">
							<div class="alert alert-info alert-block">
								<a href="#" data-dismiss="alert" class="close">×</a>
  								<h4 class="alert-heading">Информация!</h4>
  								От тук можете да добавяте/променяте/премахвате права на определени потребители към вашия сървър. Всеки потребител може да се идентифицира по различен начин и да има различни флагове (права в сървъра).<br /><br />
								Можете да се идентифицирате в сървъра като администратор чрез командата '<strong>setinfo _pw <поставената парола></strong>'
							</div>
							<?php if ($_SESSION['ftp'] != 2) { ?>
<?php if ($server_data['admins_custom'] == 0) { ?>
							<div class="box">
							<div class="box-head tabs">
								<h3>Активни Потребителски права</h3>
								<ul class="nav nav-tabs">
									<li class="active">
										Брой активни администратори: <strong><?php echo $admins_count; ?></strong>
									</li>
								</ul>
							</div>
						<div class="box-content box-nomargin">
<?php
if (empty($admins)) {
	echo '<div style="text-align: center;"><strong>Няма активни потребителски права.</strong></div>';
}
else {
?>
<table class="table table-striped table-bordered">
<thead>
<tr>
<th>Статус</th>
<th>Идентификация</th>
<th>Разпознаване по</th>
<th>Флагове</th>
<th>Изтича на</th>

<th></th>

</tr>
</thead>
<tbody>
<?php
foreach ($admins as $admin) {

	$check_admin_expire = $DB->GetOne('SELECT `expire` FROM `admins` WHERE `server_id` = ? AND `name` = ? AND `type` = ? AND `expire` <= CURDATE()', array($_SESSION['id'], $admin['name'], $admin['type']));	
	if ($admin['expire'] == '0000-00-00') {
		$status = '<span style="color: green;">Активен</span>';
		$expire_status = 'Без период';
	}
	elseif ($admin['expire'] != '0000-00-00' && empty($check_admin_expire)) {
		$status = '<span style="color: green;">Активен</span>';
		$expire_status = fixDate($admin['expire'], true);
	}
	elseif (!empty($check_admin_expire)) {
		$status = '<span style="color: red;">Изтекъл</span>';
		$expire_status = fixDate($check_admin_expire, true);
	}

if ($admin['type'] == 'a') {
	$type = 'Прякор';
}

if ($admin['type'] == 'ac') {
	$type = 'STEAM ID';
}

if ($admin['type'] == 'ab') {
	$type = 'Клан таг';
}

if ($admin['type'] == 'ad') {
	$type = 'IP адрес';
}
?>
<tr>
<td><?php echo $status; ?></td>
<td><?php echo $admin['name']; ?></td>
<td><?php echo $type; ?></td>
<td><?php echo $admin['flags']; ?></td>
<td><?php echo $expire_status; ?></td>
<td style="text-align: center;">
<input type="button" value="Премахни" name="amx_admin_delete_submit" id="amx_admin_delete_submit" class="btn btn-danger" rel="<?php echo $admin['id']; ?>">
</td>
</tr>
<?php } ?>
</tbody>
</table>
<?php } ?>						
						</div>
							
					</div>
					<div class="box">
							<div class="box-head tabs">
								<h3>Добавяне на потребителски права</h3>
							</div>	

							
<div class="box-content">
<?php if ($_SESSION['mode'] != 1) { ?>
<form action="/submit/users.php" method="POST">
<p>
<b>Рапознаване по</b><br />
<select name="type" id="type">
<option value="" selected="selected">Изберете тип на разпознаване</option>
<option value="a">Прякор (Nickname)</option>
<option value="ac">STEAM ID</option>
<option value="ab">Клан таг</option>
<option value="ad">IP адрес</option>
</select>
</p>
<p>
<label for="name"><b>Прякор | STEAM ID | IP адрес <small>( в зависимост от това какъв тип сте избрали )</small></b></label><br />
<input type="text" name="name" id="name" class="text-long"/>
</p>
<table>
<tr>
<td><label for="passwd"><b>Парола</b></label>
</td>
<td><label for="repasswd"><b>Повтори парола</b></label>
</td>
</tr>
<tr>
<td><input type="password" name="passwd" id="passwd" class="text-long"/>
</td>
<td><input type="password" name="repasswd" id="repasswd"  class="text-long"/>
</td>
</tr>
</table>
<p>
<label for="period"><b>За период от <small>( поставете 0 за без период на изтичане )</small></b></label>
<input type="text" name="period" id="period" style="width: 20px;" maxlength='3'/> дни
</p>
<p>
<b>Флагове</b><br />
<input type="checkbox" id="select_all" /> <label for="select_all"><strong>Избери всички флагове</strong></label>
<br /><br />
<input type="checkbox" name="flags_a" id="flags_a"> a - имунитет (не може да бъде кикнат/баннат/слей-нат/слап-нат и повлиян от други команди)<br />
<input type="checkbox" name="flags_b" id="flags_b" /> b - резервация (може да влезе в пълен сървър, ако има свободен резервиран слот)<br />
<input type="checkbox" name="flags_c" id="flags_c" /> c - достъп до amx_kick командата)<br />
<input type="checkbox" name="flags_d" id="flags_d" /> d - достъп до amx_ban и amx_unban командите<br />
<input type="checkbox" name="flags_e" id="flags_e" /> e - достъп до amx_slay и amx_slap командите<br />
<input type="checkbox" name="flags_f" id="flags_f" /> f - достъп до amx_map командата<br />
<input type="checkbox" name="flags_g" id="flags_g" /> g - достъп до amx_cvar командата<br />
<input type="checkbox" name="flags_h" id="flags_h" /> h - достъп до amx_cfg командата<br />
<input type="checkbox" name="flags_i" id="flags_i" /> i - достъп до amx_chat и други чат команди<br />
<input type="checkbox" name="flags_j" id="flags_j" /> j - достъп до amx_vote и други вот команди<br />
<input type="checkbox" name="flags_k" id="flags_k" /> k - достъп до sv_password (от командата amx_cvar)<br />
<input type="checkbox" name="flags_l" id="flags_l" /> l - стъп до amx_rcon командата (<span style="color: red; font-weight:bold;">Не поставяйте достъп до тази команда на недоверени играчи</span>)<br />
<input type="checkbox" name="flags_m" id="flags_m" /> m - допълнителен флаг<br />
<input type="checkbox" name="flags_n" id="flags_n" /> n - допълнителен флаг<br />
<input type="checkbox" name="flags_o" id="flags_o" /> o - допълнителен флаг<br />
<input type="checkbox" name="flags_p" id="flags_p" /> p - допълнителен флаг<br />
<input type="checkbox" name="flags_q" id="flags_q" /> q - допълнителен флаг<br />
<input type="checkbox" name="flags_r" id="flags_r" /> r - допълнителен флаг<br />
<input type="checkbox" name="flags_s" id="flags_s" /> s - допълнителен флаг<br />
<input type="checkbox" name="flags_t" id="flags_t" /> t - допълнителен флаг<br />
<input type="checkbox" name="flags_u" id="flags_j" /> u - меню достъп<br />
<input type="checkbox" name="flags_z" id="flags_z" /> z - потребител
</p>
<br />
<img src="/img/loading.gif" id="img_loading" alt="Зареждане, моля изчакайте... (Ако до две минути тази картинка не е изчезнала това означава че има някакъв проблем с услугата. Свържете се незабавно с нашия екип.">
<div class="error-box alert alert-block alert-danger" style="display: none;">Важно съобщение</div>
<input class="navigation_button btn btn-primary" id="submit" type="submit" value="Добави потребител"/>
<br /><br />
*Изтеклите потребители се деактивирате автоматично от системата в 00:00 часа
</form>
<form method="POST" action="/submit/users_custom.php">
	<input type="hidden" name="admins_custom" id="admins_custom" value="1"/>
	<input class="btn btn-danger"  type="submit" value="Превключи към ръчно добавяне"/>
	<br />
	<img src="/img/loading.gif" id="img_loading" alt="Зареждане, моля изчакайте... (Ако до две минути тази картинка не е изчезнала това означава че има някакъв проблем с услугата. Свържете се незабавно с нашия екип." />
	</fieldset>
</form>
<br />
<?php } else { ?>
		За да използвате функцията "Потребителски права" трябва да имате активен мод към сървъра. <br />Можете да го направите от <a href="/mode">Сървър мод</a>.
<?php } ?>								
						</div>
					</div>
					<?php } else { ?>
					<div class="box">
							<div class="box-head tabs">
								<h3>Редактиране на потребителски правила чрез файла users.ini</h3>
							</div>	

							
<div class="box-content">
<?php if ($_SESSION['mode'] != 1) { ?>
<form action="/submit/users.php" method="POST">
<p style="text-align: center;">
	<textarea style="width: 800px; height: 350px;" name="admins">
<?php
	echo '<pre>' . $login_sftp->get('/home/servers/cstrike/' . $_SESSION['id'] . '/cstrike/addons/amxmodx/configs/users.ini') . '</pre>';
?>						
	
	</textarea>
</p>

<div style="text-align: center;">
<img src="/img/loading.gif" id="img_loading" alt="Зареждане, моля изчакайте... (Ако до две минути тази картинка не е изчезнала това означава че има някакъв проблем с услугата. Свържете се незабавно с нашия екип.">
<div class="error-box alert alert-block alert-danger" style="display: none;">Важно съобщение</div>
<input class="navigation_button btn btn-primary" id="submit" type="submit" value="Изпрати"/>
</div>	
</form>
<form method="POST" action="/submit/users_custom.php">
	<input type="hidden" name="admins_custom" id="admins_custom" value="0"/>
	<input class="btn btn-danger"  type="submit" value="Превключи към автоматично добавяне"/>
	<br />
	<img src="/img/loading.gif" id="img_loading" alt="Зареждане, моля изчакайте... (Ако до две минути тази картинка не е изчезнала това означава че има някакъв проблем с услугата. Свържете се незабавно с нашия екип." />
	</fieldset>
</form>
<br />
*При ръчно редактиране на файла с потребителските права опцията за период не е активна.<br />
*СМСХостинг.БГ не поема отговорност при сгрешена конфигурация от страна на потребителя при активна опция за ръчно редактиране на правата.

<?php } else { ?>
		За да използвате функцията "Потребителски права" трябва да имате активен мод към сървъра. <br />Можете да го направите от <a href="/mode">Сървър мод</a>.
<?php } ?>								
						</div>
					</div>
					<?php } ?>
			<?php } else { ?>
				Тази функция не е налична поради пълният FTP достъп към сървъра.
			<?php } ?>
				</div>
			</div>			
		</div>	
	</div>
<?php require_once('../templates/footer.php'); ?>
