<?php 
	require_once('../templates/header.php'); 
	$server_data = $DB->GetRow('SELECT * FROM `servers` WHERE `id` = ?', array($_SESSION['id']));
?>
<div class="container-fluid">
		<div class="content">
				<?php require_once('./includes/etc/header-stats.php');  ?>		
			<div class="row-fluid">
				<div class="span12">
					<div class="alert alert-info alert-block">
						<a href="#" data-dismiss="alert" class="close">×</a>
						<h4 class="alert-heading">Информация!</h4>
						От тук можете да изритате играч от вашия сървър. Ако желаете след изритването можете да поставите временна или перманентна забрана."
					</div>	
<?php if ($_SESSION['ftp'] != 2) { ?>	
<?php if ($server_data['kickban_custom'] == 0) { ?>				
					<div class="box">
						<div class="box-head">
							<h3>Изритване/Забрана на играч (kick/ban)</h3>
						</div>
						<div class="box-content">
							<?php 
							if ($result["activeplayers"] == 0) {
							echo '<strong>Няма активни играчи за изритване (Kick)</strong>';
							}
							else {
							?>
							
							<form action="/submit/kickban.php" method="POST">

							<label for="player">Избери играч:</label> 
							<select name="player" id="player">
							<option value="" selected="selected">Изберете играч</option>
							<?php 
								for($i = 1; $i <= $result["activeplayers"]; $i++)
								{
								  $tmp = $line[$i + 6];
								  if(substr_count($tmp, "#") <= 0)
									break;
								  $begin = strpos($tmp, "\"") + 1;
								  $end = strrpos($tmp, "\"");
								  $result[$i]["name"] = substr($tmp, $begin, $end - $begin);
								  $tmp = trim(substr($tmp, $end + 1));

								  //ID
								  $end = strpos($tmp, " ");
								  $result[$i]["id"] = substr($tmp, 0, $end);
								  $tmp = trim(substr($tmp, $end));

								  //WonID
								  $end = strpos($tmp, " ");
								  $result[$i]["wonid"] = substr($tmp, 0, $end);
								  $tmp = trim(substr($tmp, $end));

								  //Frag
								  $end = strpos($tmp, " ");
								  $result[$i]["frag"] = substr($tmp, 0, $end);
								  $tmp = trim(substr($tmp, $end));

								  //Time
								  $end = strpos($tmp, " ");
								  $result[$i]["time"] = substr($tmp, 0, $end);
								  $tmp = trim(substr($tmp, $end));

								  //Ping
								  $end = strpos($tmp, " ");
								  $result[$i]["ping"] = substr($tmp, 0, $end);
								  $tmp = trim(substr($tmp, $end));

								  //Loss
								  $tmp = trim(substr($tmp, $end));

								  //Adress
								  $result[$i]["adress"] = $tmp;
								  
								  $ip = explode(':', $result[$i]["adress"]);
								  
								  echo '<option value="' . $result[$i]["name"] . '::;;:' . $ip[0] . '">#' . $i . ' ' . $result[$i]["name"] . ' - ' . $result[$i]["adress"] . ' Фрагове: ' . $result[$i]['frag'] . ' | Latency: ' . $result[$i]['ping'] . '</option>';
								}
							?>
							</select>

							<p>
							<label for="reason">Причина:</label>
							<input type="text" name="reason" class="text-long" id="reason" />
							</p>
							<input type="checkbox" value="yes" name="ban_check" /> Забрани достъп след изритване
							<br /><br />
							<div id="ban_details" style="display: none;">
							<label for="ban_time">Време на забраната (минути):</label>
							<input type="text" style="width: 50px;" id="ban_time" name="ban_time" value="0" />
							<br />
							<small>*** 0 = постоянен ( перманентен ) <br />
							*** 1440 = 24 часа <br />
							*** 10080 = Една седмица <br />
							*** 312480 = Един месец </small>
							</div>
							<br />
							<p>
							<input  class="navigation_button btn btn-primary" type="submit" value="Изритай / Kick" />
							</form>
<form method="POST" action="/submit/kickban_custom.php">
	<input type="hidden" name="kickban_custom" id="kickban_custom" value="1"/>
	<input class="btn btn-danger"  type="submit" value="Превключи към ръчно добавяне"/>
	<br />
	<img src="/img/loading.gif" id="img_loading" alt="Зареждане, моля изчакайте... (Ако до две минути тази картинка не е изчезнала това означава че има някакъв проблем с услугата. Свържете се незабавно с нашия екип." />
	</fieldset>
</form>							
							<?php
							}
							?>
							</p>
						</div>
					</div>
					<br />
<div class="box">
						<div class="box-head">
							<h3>Списък със забранени играчи</h3>
						</div>
						<div class="box-content">
							<p>
							<?php echo '<pre>' . $server->RconCommand("listip") . '</pre>'; ?>
							</p>
							
							<form action="/submit/ban_all.php" method="POST">
							<input type="hidden" name="unban_all" />
							<input class="navigation_button btn btn-primary" type="submit" value="Изтисти всички забрани от сървъра" /><br />
							*Рестартира сървъра
							</form>

						</div>
					</div>
<?php } else { ?>
					<div class="box">
							<div class="box-head tabs">
								<h3>Добавяне на играчи към списък със забрани чрез файла listip.cfg	</h3>
							</div>	

							
<div class="box-content">

<form action="/submit/kickban.php" method="POST">
<p style="text-align: center;">
	<textarea style="width: 800px; height: 350px;" name="kickbans">
<?php
	echo '<pre>' . $login_sftp->get('/home/servers/cstrike/' . $_SESSION['id'] . '/cstrike/listip.cfg') . '</pre>';
?>						
	
	</textarea>
</p>

<div style="text-align: center;">
<img src="/img/loading.gif" id="img_loading" alt="Зареждане, моля изчакайте... (Ако до две минути тази картинка не е изчезнала това означава че има някакъв проблем с услугата. Свържете се незабавно с нашия екип.">
<div class="error-box alert alert-block alert-danger" style="display: none;">Важно съобщение</div>
<input class="navigation_button btn btn-primary" id="submit" type="submit" value="Изпрати"/>
</div>	
</form>
<form method="POST" action="/submit/kickban_custom.php">
	<input type="hidden" name="kickban_custom" id="kickban_custom" value="0"/>
	<input class="btn btn-danger"  type="submit" value="Превключи към автоматично добавяне"/>
	<br />
	<img src="/img/loading.gif" id="img_loading" alt="Зареждане, моля изчакайте... (Ако до две минути тази картинка не е изчезнала това означава че има някакъв проблем с услугата. Свържете се незабавно с нашия екип." />
	</fieldset>
</form>
<br />
*СМСХостинг.БГ не поема отговорност при сгрешена конфигурация от страна на потребителя при активна опция за ръчно редактиране на файла.
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