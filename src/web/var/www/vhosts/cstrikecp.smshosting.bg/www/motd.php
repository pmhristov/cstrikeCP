<?php 
require_once('../templates/header.php'); 
$motd_val = $DB->GetOne('SELECT `motd` FROM `servers` WHERE `id` = ?', $_SESSION['id']);
?>

<div class="container-fluid">
		<div class="content">
				<?php require_once('./includes/etc/header-stats.php');  ?>		
			<div class="row-fluid">
				<div class="span12">
					<div class="alert alert-info alert-block">
						<a href="#" data-dismiss="alert" class="close">×</a>
						<h4 class="alert-heading">Информация!</h4>
						Тук можете да променяте началната страница, която всеки играч вижда щом влезе във вашия сървър.
					</div>	
<?php if ($_SESSION['ftp'] != 2) { ?>							
					
					<div class="box">
						<div class="box-head">
							<h3>Промяна на сървър MOTD (Message of the day)</h3>
						</div>
						<div class="box-content">
							<form action="/submit/motd.php" method="post">
							<p><b>MOTD код (html)</b><br />
								<textarea rows="15" style="width: 560px;" id="motd_text" name="motd_text"><?php echo $motd_val; ?></textarea>
							</p>							
							<p>
							<div class="error-box alert alert-block alert-danger" style="display: none;">Важно съобщение</div>
							<input  class="navigation_button btn btn-primary" id="submit" type="submit" value="Промени MOTD" />
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