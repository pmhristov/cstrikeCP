<?php require_once('../templates/header.php'); ?>
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
					<div class="box">
						<div class="box-head">
							<h3>Промяна на сървър мод</h3>
						</div>
						<div class="box-content">
							<?php 
							if ( $result["activeplayers"] == 0 ) {
								echo '<strong>Няма активни играчи за забрана (Ban)</strong>';
							}
							else {
							?>
							<form action="/submit/ban.php" method="POST">
							<p>
							<label for="ip">Избери играч:</label>
							 <select name="ip" id="ip">
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
								  
								  echo '<option value="' . $ip[0] . '">#' . $i . ' ' . $result[$i]["name"] . ' - ' . $result[$i]["adress"] . '</option>';
								}
							?>
							</select>
							</p>
							
							<p>
							Лист със забранени адреси:<br />
							<?php echo $server->RconCommand("listip"); ?>
							</p>
							<p>
							<input class="navigation_button btn btn-primary" type="submit" value="Забрани / Бан" />
							<?php
							}
							?>
							</p>
							</form>

							</fieldset>
							<fieldset>
							<form action="/submit/ban_all.php" method="POST">
							<input type="hidden" name="unban_all" />
							<input class="navigation_button btn btn-primary" type="submit" value="Изтисти всички забрани от сървъра" />
							</form>
						</div>
					</div>
				</div>
			</div>			
		</div>	
	</div>
<?php require_once('../templates/footer.php'); ?>