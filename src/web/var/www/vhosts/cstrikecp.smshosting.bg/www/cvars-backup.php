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
						Тука можете да настройките основните променливи за вашия сървър, които ще бъдат активни дори и след рестарт.
					</div>				
					<div class="box">
						<div class="box-head">
							<h3>Персонални CVARs</h3>
						</div>
						<div class="box-content">
		<form action="/submit/cvars.php" method="POST">
			<table cellpadding="0" cellspacing="0">
							<tr>
                                <td>mp_roundtime</td>
                                <td class="action"><select name="mp_roundtime"><option value="1">1 минута</option><option value="2">2 минути</option><option value="3">3 минути</option><option value="4">4 минути</option><option value="5">5 минути</option><option value="6">6 минути</option><option value="7">7 минути</option><option value="9">9 минути</option></select></td>
                            </tr>                        
							<tr class="odd">
                                <td>mp_timelimit</td>
                                <td class="action"><select name="mp_timelimit"><option value="0">0 (без лимит)</option><option value="15">15 минути</option><option value="30">30 минути</option><option value="45">45 минути</option><option value="60">60 минути</option></select></td>
                            </tr>   
							<tr>
                                <td>pausable</td>
                                <td class="action"><select name="pausable"><option value="0">0 (Не)</option><option value="1">1 (Да)</option></select></td>
                            </tr>                        
							<tr class="odd">
                                <td>sv_voiceenable</td>
                                <td class="action"><select name="sv_voiceenable"><option value="0">0 (Не)</option><option value="1">1 (Да)</option></select></td>
                            </tr> 
							<tr>
                                <td>mp_friendlyfire</td>
                                <td class="action"><select name="mp_friendlyfire"><option value="0">0 (Не)</option><option value="1">1 (Да)</option></select></td>
                            </tr>                        
							<tr class="odd">
                                <td>mp_limitteams</td>
                                <td class="action"><select name="mp_limitteams"><option value="0">0 </option><option value="1">1</option><option value="2">2</option></select></td>
                            </tr> 
							<tr>
                                <td>mp_autoteambalance</td>
                                 <td class="action"><select name="mp_autoteambalance"><option value="0">0 </option><option value="1">1</option><option value="2">2</option></select></td>
                            </tr>
							<tr class="odd">
                                <td>mp_freezetime</td>
                                <td class="action"><select name="mp_freezetime"><option value="0">0 (изключен)</option><option value="3">3 секунди</option><option value="5">5 секунди</option><option value="7">7 секунди</option><option value="10">10 секунди</option></select></td>
                            </tr>
							<tr class="odd">
                                <td>sv_airaccelerate</td>
                                <td class="action"><select name="sv_airaccelerate"><option value="10">10</option><option value="100">100</option><option value="1000">1000</option></select></td>
                            </tr> 							
							<tr>
                                <td>mp_buytime</td>
                                <td class="action"><select name="mp_buytime"><option value="0.25">15 секунди</option><option value="0.5">30 секунди</option><option value="1">60 секунди</option><option value="1.5">90 секунди</option><option value="2">120 секунди</option></select></td>
                            </tr>                        
							<tr class="odd">
                                <td>mp_startmoney</td>
                                <td class="action"><select name="mp_startmoney"><option value="800">$800</option><option value="1000">$1000</option><option value="2000">$2000</option><option value="3000">$3000</option><option value="4000">$4000</option><option value="5000">$5000</option><option value="6000">$6000</option><option value="7000">$7000</option><option value="10000">$10000</option><option value="12000">$12000</option><option value="14000">$14000</option><option value="16000">$16000</option></select></td>
                            </tr> 	
							<tr>
                                <td>mp_c4timer</td>
                                <td class="action"><select name="mp_c4timer"><option value="35">35 секунди</option><option value="45">45 секунди</option><option value="60">60 секунди</option></select></td>
                            </tr>                        							
                        </table><br />
						<input class="navigation_button btn btn-primary" type="submit" value="Изпрати" /><br />
</form>
Повече информация за командите можете да намерите на <a target="_blank" href="http://help.smshosting.bg/category/%D0%B3%D0%B5%D0%B9%D0%BC-%D1%81%D1%8A%D1%80%D0%B2%D1%8A%D1%80/">този</a> адрес.
<br /><br />
						</div>
					</div>
				</div>
			</div>			
		</div>	
	</div>
<?php require_once('../templates/footer.php'); ?>