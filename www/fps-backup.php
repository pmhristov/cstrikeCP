<?php require_once('../templates/header.php'); ?>
<div class="container-fluid">
		<div class="content">
				<?php require_once('./includes/etc/header-stats.php');  ?>		
			<div class="row-fluid">
				<div class="span12">
					<div class="alert alert-info alert-block">
						<a href="#" data-dismiss="alert" class="close">×</a>
						<h4 class="alert-heading">Информация!</h4>
							Колкото по - висок е FPS рейтинга на вашия сървър толкова по - гладка е играта в него. Можете да избирате от следните стойности:
					</div>				
					<div class="box">
						<div class="box-head">
							<h3>FTP достъп</h3>
						</div>
						<div class="box-content">						
								<form action="/submit/fps.php" method="POST">
								<h3>FPS рейтинг в момента: <?php echo $srv_details['fps']; ?></h3>
								<br />
								<table width="30%">
								<tr>
								<td style="text-align: center;"><input name="fps_rating" type="radio" id="fps_100" value="100" /></td>
								<td style="text-align: center;"><input name="fps_rating" type="radio" id="fps_960" value="960" /></td>
								<td style="text-align: center;"><input name="fps_rating" type="radio" id="fps_1000" value="1000" /></td>
								</tr>
								<tr>
								<td style="text-align: center;"><label for="fps_100"><img src="/img/fps/1.png" alt="" title="100 FPS рейтинг" /></label></td>
								<td style="text-align: center;"><label for="fps_960"><img src="/img/fps/2.png" alt="" title="~960 FPS рейтинг" /></label></td>
								<td style="text-align: center;"><label for="fps_1000"><img src="/img/fps/3.png" alt="" title="1000 FPS рейтинг" /></label></td>
								</tr>
								</table>
								<br />
								<img src="/img/loading.gif" id="img_loading" alt="Зареждане, моля изчакайте... (Ако до две минути тази картинка не е изчезнала това означава че има някакъв проблем с услугата. Свържете се незабавно с нашия екип." />								
								<div class="error-box alert alert-block alert-danger" style="display: none;">Важно съобщение</div>
								<input class="navigation_button btn btn-primary" id="submit" type="submit" value="Промени FPS рейтинг"/>

								</form>
						</div>
					</div>
				</div>
			</div>			
		</div>	
	</div>
<?php require_once('../templates/footer.php'); ?>
	


