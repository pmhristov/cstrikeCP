<?php 
	require_once('../templates/header.php'); 
?>
<div class="container-fluid">
		<div class="content">
				<?php require_once('./includes/etc/header-stats.php');  ?>	
			<div class="row-fluid">
				<div class="span12">
					<div class="alert alert-info alert-block">
						<a href="#" data-dismiss="alert" class="close">×</a>
						<h4 class="alert-heading">Информация!</h4>
						Тука можете да изберете мода на вашия сървър и да го промените с едно натискане на бутона без да е нужно да се тормозите с излишни настройки.
					</div>	
					<?php if ($_SESSION['ftp'] != 2) { ?>					
					<div class="box">
						<div class="box-head tabs">
							<h3>Промяна на сървър мод</h3>
								<ul class="nav nav-tabs">
									<li class="active">
									</li>
								</ul>							
						</div>
						<div class="box-content">
							<form action="/submit/mode.php" method="POST" id="chmodeform">
								<fieldset>
								<label for="mode"><b>Активен мод:</b> <b><?php
								if ($_SESSION['mode'] == 1) {
									echo '<span style="color: red;">Няма активен</span>';
								}
								else {
									echo '<span style="color: green;">' . $mode_labels[$_SESSION['mode']][1] . '</span>';
								}
								 ?></b></label><br /><br />
								<select name="mode" style="width: 500px;" id="mode" class='cho' title="Изберете мод">
									<option value="" selected="selected">Изберете желания мод</option>
									<?php
									foreach ($mode_labels as $label) {
									?>
									<option value="<?php echo $label[0]; ?>"><?php echo $label[1]; ?></option>
									<?php
									}
									?>
								</select>
								<br />
								<div id="mode_info">
								Изберете желания мод за да видите основната информация за него.
								</div>
								<div class="error-box alert alert-block alert-danger" style="display: none;">Важно съобщение</div>
								<input class="navigation_button btn btn-primary" id="submit" type="submit" value="Промени мод"/> 
							</form>
							
							<br />
							<img src="/img/loading.gif" id="img_loading" alt="Зареждане, моля изчакайте... (Ако до две минути тази картинка не е изчезнала това означава че има някакъв проблем с услугата. Свържете се незабавно с нашия екип." />							
							<br />
							<form method="POST" action="/submit/mode_remove.php">
								<input type="hidden" name="mode_remove" id="mode_remove" />
								<input class="btn btn-danger"  type="submit" value="Премахни сървър мод"/>
								<br />
								<small><span style="color: red;"><b>Това действие ще премахне вашия мод изцяло.</b></span></small>
								<br />
								<img src="/img/loading.gif" id="img_loading" alt="Зареждане, моля изчакайте... (Ако до две минути тази картинка не е изчезнала това означава че има някакъв проблем с услугата. Свържете се незабавно с нашия екип." />
								<br />
								
								<strong>*При промяна на мода на сървъра всички плъгини, пренасочвания и античиит системи се запазват.</strong><br />
								<strong>*Сървърите са изчистени от всякакви видове рекламни съобщения</strong><br />
								<strong>*При проблеми със смяната на мода се свържете с нас от посочените контакти в сайта.</strong>
								</fieldset>
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
	