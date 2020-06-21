<?php 
	require_once('../templates/header.php'); 
	$scrollmsg_val = $DB->GetOne('SELECT `scrollmsg_text` FROM `servers` WHERE `id` = ?', $_SESSION['id']);
?>
<div class="container-fluid">
		<div class="content">
				<?php require_once('./includes/etc/header-stats.php');  ?>		
			<div class="row-fluid">
				<div class="span12">
					<div class="alert alert-info alert-block">
						<a href="#" data-dismiss="alert" class="close">×</a>
						<h4 class="alert-heading">Информация!</h4>
						Scroll съобщение представлява текста, който се показва в долната част на вашия монитор по време на игра. Може да бъде използван за реклама или промотиране на услуга.
					</div>		
<?php if ($_SESSION['ftp'] != 2) { ?>							
					
					<div class="box">
						<div class="box-head">
							<h3>Scroll съобщение</h3>
						</div>
						<div class="box-content">
							<form action="/submit/scrollmsg.php" method="post">	
							<b>Текста който да се показва:</b><br />
							<input type="text" name="scrollmsg_text" id="scrollmsg_text" value="<?php echo $scrollmsg_val; ?>" style="width: 400px;"/>
							</p>
							<p><b>Интервал през който да се показва съобщението</b><br />
								<select name="scrollmsg_time" id="scrollmsg_time" >
									<option value="60" selected="selected">1 минута</option>
									<option value="120">2 минути</option>
									<option value="180">3 минути</option>
									<option value="300">5 минути</option>
									<option value="600">10 минути</option>
									<option value="1200">20 минути</option>
								</select>
							</p>
							<p>
							<div class="error-box alert alert-block alert-danger" style="display: none;">Важно съобщение</div>
							<input class="navigation_button btn btn-primary" id="submit" type="submit" value="Изпрати" />
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