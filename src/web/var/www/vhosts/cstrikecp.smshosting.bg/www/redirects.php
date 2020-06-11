<?php 
	require_once('../templates/header.php'); 
	$redirects = $DB->GetAll('SELECT * FROM `redirects` WHERE `server_id` = ?', $_SESSION['id']);
?>

<div class="container-fluid">
		<div class="content">
				<?php require_once('./includes/etc/header-stats.php');  ?>		
			<div class="row-fluid">
				<div class="span12">
					<div class="alert alert-info alert-block">
						<a href="#" data-dismiss="alert" class="close">×</a>
						<h4 class="alert-heading">Информация!</h4>
					        Чрез тази опция давате възможност на играчите да се свържат във други сървъри, избрани от Вас чрез командата /server или /servers  
					</div>		
<?php if ($_SESSION['ftp'] != 2) { ?>						
					<div class="box">
					<div class="box-head tabs">
						<h3>Пренасочвания</h3>
						<ul class="nav nav-tabs">
							<li class="active">
								Брой пренасочвания: <strong><?php echo $redirects_count; ?></strong>
							</li>
						</ul>
					</div>
					<div class="box-content box-nomargin">
						<?php					
						if (empty($redirects)) {
							echo '<div style="text-align: center;"><strong>Няма добавени пренасочвания</strong></div>';
						}
						else {
						?>

						<table class="table table-striped table-bordered">
						<tr>
						<td>Име на сървъра</th>
						<td>Адрес</th>
						<td class="action"></th>
						</tr>
						<?php
						foreach ($redirects as $redirect) {
						?>
						<tr>
						<td><?php echo $redirect['hostname']; ?></td>
						<td><?php echo $redirect['addr']; ?></td>
						<td style="text-align: center;">
						<input type="button" value="Премахни"  class="btn btn-danger" name="amx_redirect_delete_submit" id="amx_redirect_delete_submit" rel="<?php echo $redirect['id']; ?>">
						</td>
						</tr>
						<?php } ?>
						</table>	
						<?php } ?>					
						</div>
					</div>
					<div class="box">					
						<div class="box-head">
							<h3>Добави пренасочване</h3>
						</div>
						<div class="box-content">
							<?php 
							if ($_SESSION['mode'] != 1) { 
							?>
							<br />
							<p>
							<form action="/submit/redirect.php" method="post">
							<p>
							<label for="hostname"> <b>Име на сървъра</b></label>
							<input type="text" name="hostname" class="text-long"/>
							</p>
							<p>
							<label for="hostname"> <b>Адрес на сървъра (Пример: 212.50.15.243:27016)</b></label>
							<input type="text" name="addr" class="text-long"/>
							</p>
							<div class="error-box alert alert-block alert-danger" style="display: none;">Важно съобщение</div>
							<input class="navigation_button btn btn-primary" type="submit" value="Изпрати"/>
							</form>
							<img src="/img/loading.gif" id="img_loading" alt="Зареждане, моля изчакайте... (Ако до две минути тази картинка не е изчезнала това означава че има някакъв проблем с услугата. Свържете се незабавно с нашия екип." />
							<?php } else { ?>
							<fieldset>За да използвате функцията "Пренасочване" трябва да имате активиран мод към сървъра. <br />Можете да активирате мод към вашия сървър от менюто "<a href="/mode">Сървър мод</a>.</fieldset>"
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
	


<!-- h2 stays for breadcrumbs -->

