<?php require_once('../templates/header.php'); ?>
<div class="container-fluid">
		<div class="content">
				<?php require_once('./includes/etc/header-stats.php');  ?>		
			<div class="row-fluid">
				<div class="span12">
					<div class="alert alert-info alert-block">
						<a href="#" data-dismiss="alert" class="close">×</a>
						<h4 class="alert-heading">Информация!</h4>
						Тази екстра позволява да създавате динамичен банер на вашия сървър който ще се променя в зависимост от картата, играчите, името и статуса на самия сървър. Чрез този банер можете да рекламирате вашия сървър и по този начин играчите винаги да следят какво се случва в него.
					</div>				
					<div class="box">
						<div class="box-head">
							<h3>Динамичен банер</h3>
						</div>
						<div class="box-content">

						
						</fieldset>
						<h3>Списък с генерирани банери</h3>
						<fieldset>
						<?php
							$banners = $DB->GetAll('SELECT * FROM `gametracker` WHERE `server_id` = ?', $_SESSION['id']);
							if (empty($banners)) {
								echo '<div style="text-align: center;">Все още няма генерирани банери.</div>';
							}
							else {
								foreach ($banners as $banner) {
							?>

									<b>Банер #<?php echo $banner['id']; ?> код</b><br />
									<textarea style="width: 630px; height: 15px;" readonly="readonly"><img src="http://gametracker.smshosting.bg/<?php echo $_SESSION['ip'] . ':' . $_SESSION['port'] . '/' . $banner['id'] . '.png'; ?>" /></textarea><br />
							<?php		
								}
							}
						?>
						<br />
						</fieldset>
						<h3>Създай банер</h3>
						<form action="/submit/gametracker.php" method="post">
						<fieldset>
								<div style="text-align: center;">
									<select name="img" id="img">
										<option value="" selected="selected">Изберете банер</option>
										<option value="1">Банер #1</option>
										<option value="2">Банер #2</option>
									</select><br /><br />
									<input  class="navigation_button btn btn-primary" type="button" id="refresh_button" value="Прегледай преди поръчка" style="width: 200px; height: 30px;"/>
								<div id="img_field" style="display: none;"></div><br />
								<span style="font-size: 10px;"><a href="/img/gametracker/example.jpg" target="_blank">Позиции на текста</a></span>	
								</div>
								
						</fieldset>	
						<p>
						<u>Име на сървъра</u>
							<table>
								<tr>
									<td>
									<b>Тип на шрифта</b><br />
										<select name="font_hostname" id="font_hostname">
											<option value="" selected="selected">Изберете шрифт</option>
											<?php
												$files = scandir($fonts_dir);
												foreach ($files as $file) {
													$file = explode('.', $file);
													if ($file[0] != '') {
											?>
														<option value="<?php echo $file[0] . '.' . $file[1]; ?>"><?php echo strtolower($file[0]); ?></option>
													<?php
													}
												}
											?>
										</select>
									</td>	
									<td>
										<b>Големина на шрифта</b><br />
										<select name="size_hostname" id="size_hostname">
											<option value="" selected="selected">Изберете големина</option>
											<?php 
												for ($num = 5; $num <= 35; $num++) {
													?>
													<option value="<?php echo $num; ?>"><?php echo $num; ?></option>
													<?php
												}
											?>
										</select>
									</td>	
									<td>
										<b>Цвят на шрифта</b><br />
										<select name="color_hostname" id="color_hostname">
											<option value="" selected="selected">Изберете цвят</option>
											<?php 
												foreach ($colors as $color) {
													?>
													<option value="<?php echo $color[0]; ?>"><?php echo $color[1]; ?></option>
													<?php
												}
											?>
										</select>
									</td>				
								</tr>
							</table>
							<br />
						</p>
						<p>
						<u>Адрес на сървъра</u>
							<table>
								<tr>
									<td>
									<b>Тип на шрифта</b><br />
										<select name="font_addr" id="font_addr">
											<option value="" selected="selected">Изберете шрифт</option>
											<?php
												$files = scandir($fonts_dir);
												foreach ($files as $file) {
													$file = explode('.', $file);
													if ($file[0] != '') {
											?>
														<option value="<?php echo $file[0] . '.' . $file[1]; ?>"><?php echo strtolower($file[0]); ?></option>
													<?php
													}
												}
											?>
										</select>
									</td>	
									<td>
										<b>Големина на шрифта</b><br />
										<select name="size_addr" id="size_addr">
											<option value="" selected="selected">Изберете големина</option>
											<?php 
												for ($num = 5; $num <= 35; $num++) {
													?>
													<option value="<?php echo $num; ?>"><?php echo $num; ?></option>
													<?php
												}
											?>
										</select>
									</td>	
									<td>
										<b>Цвят на шрифта</b><br />
										<select name="color_addr" id="color_addr">
											<option value="" selected="selected">Изберете цвят</option>
											<?php 
												foreach ($colors as $color) {
													?>
													<option value="<?php echo $color[0]; ?>"><?php echo $color[1]; ?></option>
													<?php
												}
											?>
										</select>
									</td>				
								</tr>
							</table>
							<br />
						</p>
						<p>
						<u>Играчи</u>
							<table>
								<tr>
									<td>
									<b>Тип на шрифта</b><br />
										<select name="font_players" id="font_players">
											<option value="" selected="selected">Изберете шрифт</option>
											<?php
												$files = scandir($fonts_dir);
												foreach ($files as $file) {
													$file = explode('.', $file);
													if ($file[0] != '') {
											?>
														<option value="<?php echo $file[0] . '.' . $file[1]; ?>"><?php echo strtolower($file[0]); ?></option>
													<?php
													}
												}
											?>
										</select>
									</td>	
									<td>
										<b>Големина на шрифта</b><br />
										<select name="size_players" id="size_players">
											<option value="" selected="selected">Изберете големина</option>
											<?php 
												for ($num = 5; $num <= 35; $num++) {
													?>
													<option value="<?php echo $num; ?>"><?php echo $num; ?></option>
													<?php
												}
											?>
										</select>
									</td>	
									<td>
										<b>Цвят на шрифта</b><br />
										<select name="color_players" id="color_players">
											<option value="" selected="selected">Изберете цвят</option>
											<?php 
												foreach ($colors as $color) {
													?>
													<option value="<?php echo $color[0]; ?>"><?php echo $color[1]; ?></option>
													<?php
												}
											?>
										</select>
									</td>				
								</tr>
							</table>
							<br />
						</p>
						<p>
						<u>Карта</u>
							<table>
								<tr>
									<td>
									<b>Тип на шрифта</b><br />
										<select name="font_map" id="font_map">
											<option value="" selected="selected">Изберете шрифт</option>
											<?php
												$files = scandir($fonts_dir);
												foreach ($files as $file) {
													$file = explode('.', $file);
													if ($file[0] != '') {
											?>
														<option value="<?php echo $file[0] . '.' . $file[1]; ?>"><?php echo strtolower($file[0]); ?></option>
													<?php
													}
												}
											?>
										</select>
									</td>	
									<td>
										<b>Големина на шрифта</b><br />
										<select name="size_map" id="size_map">
											<option value="" selected="selected">Изберете големина</option>
											<?php 
												for ($num = 5; $num <= 35; $num++) {
													?>
													<option value="<?php echo $num; ?>"><?php echo $num; ?></option>
													<?php
												}
											?>
										</select>
									</td>	
									<td>
										<b>Цвят на шрифта</b><br />
										<select name="color_map" id="color_map">
											<option value="" selected="selected">Изберете цвят</option>
											<?php 
												foreach ($colors as $color) {
													?>
													<option value="<?php echo $color[0]; ?>"><?php echo $color[1]; ?></option>
													<?php
												}
											?>
										</select>
									</td>				
								</tr>
							</table>
							<br />
						</p>
						<p>
						<u>Статус</u>
							<table>
								<tr>
									<td>
									<b>Тип на шрифта</b><br />
										<select name="font_status" id="font_status">
											<option value="" selected="selected">Изберете шрифт</option>
											<?php
												$files = scandir($fonts_dir);
												foreach ($files as $file) {
													$file = explode('.', $file);
													if ($file[0] != '') {
											?>
														<option value="<?php echo $file[0] . '.' . $file[1]; ?>"><?php echo strtolower($file[0]); ?></option>
													<?php
													}
												}
											?>
										</select>
									</td>	
									<td>
										<b>Големина на шрифта</b><br />
										<select name="size_status" id="size_status">
											<option value="" selected="selected">Изберете големина</option>
											<?php 
												for ($num = 5; $num <= 35; $num++) {
													?>
													<option value="<?php echo $num; ?>"><?php echo $num; ?></option>
													<?php
												}
											?>
										</select>
									</td>	
									<td>
										<b>Цвят на шрифта</b><br />
										<select name="color_status" id="color_status">
											<option value="" selected="selected">Изберете цвят</option>
											<?php 
												foreach ($colors as $color) {
													?>
													<option value="<?php echo $color[0]; ?>"><?php echo $color[1]; ?></option>
													<?php
												}
											?>
										</select>
									</td>				
								</tr>
							</table>
							<br />
						</p>
						<br />
						<img src="/img/icons/essen/16/bank.png" /> <span style="color: green;"><strong>Цена:</strong></span><br />
						<strong>1.20 лв с ДДС</strong>							
						<br /><br />								
									<p>
									<div class="error-box alert alert-block alert-danger" style="display: none;">Важно съобщение</div>
										<input class="navigation_button btn btn-primary" type="submit" value="Генерирай банер">
									</p>
							</form>
						</div>
					</div>
				</div>
			</div>			
		</div>	
	</div>
<?php require_once('../templates/footer.php'); ?>
	