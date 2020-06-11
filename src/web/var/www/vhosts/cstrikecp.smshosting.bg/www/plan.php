<?php require_once('../templates/header.php'); ?>
<div class="container-fluid">
		<div class="content">
				<?php require_once('./includes/etc/header-stats.php');  ?>		
			<div class="row-fluid">
				<div class="span12">
					<div class="alert alert-info alert-block">
						<a href="#" data-dismiss="alert" class="close">×</a>
						<h4 class="alert-heading">Информация!</h4>
						Тук можете да намерите основна информация за всеки план и как да преминете на различен план от сегашния без да загубите адреса или информация на сървъра.
					</div>				
					<div class="box">
						<div class="box-head">
							<h3>Промяна на план</h3>
						</div>
						<h4>Текущ план: <?php echo $plan_labels[$_SESSION['plan']][0]; ?></h4>
						<br />
						Ако параметрите от плана на вашия сървър не ви достигат и желаете по - голям брой игрални слотове и сървър за по - голям период, можете да промените плана на вашия сървър без да загубите адреса и каквато и да е информация или настройка за вашия сървър.
						<br /><br />
						Плановете можете да разгледате <a href="http://www.smshosting.bg/cstrike" target="_blank">тук</a>.
						<br /><br />
						За да промените плана на вашия сървър е нужно да се свържете с нас на един от посочените <a href="http://www.smshosting.bg/contacts" target="_blank">контакти</a>.
					</div>
				</div>
			</div>			
		</div>	
	</div>
<?php require_once('../templates/footer.php'); ?>
	