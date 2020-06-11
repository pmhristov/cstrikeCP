<?php
require_once('../../includes/core.php');

$status = 0;
$msg = '';
$curr_date = date('Y-m-d');


if (!empty($_POST['service'])) {
	$service = explode('-', preg_replace('/[^a-z0-9 -]/i', '', $_POST['service']));
	
	$service_file = INCLUDE_SERVICE_DIR . $service[0] . (count($service) > 1 ? '/' . $service[1] : '') . DEFAULT_EXT;

	if (file_exists($service_file)) {
		require($service_file);
	}
	else {
		$msg = 'неактивна или несъщесътвуваща услуга!';
	}
}
else {
	$msg = 'не е избрана услуга!';
}

if (isset($_POST['nojs'])) {
	die('JavaScript не е поддържан или е деактивиран от вашия браузър. За да функционира сайта правилно трябва да активирате JavaScript.');
}
else {
if (1 === $status) {
	$msg = '<div id="service_result">' . $msg . '</div>';
}
else {
	$msg = 'Грешка: ' . $msg;
}


echo json_encode(
	array(
		'status' => $status,
		'msg' => $msg,
	)
);
}
?>
