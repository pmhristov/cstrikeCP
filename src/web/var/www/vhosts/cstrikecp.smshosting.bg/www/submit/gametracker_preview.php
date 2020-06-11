<?php
session_start();

$font_path = '/var/www/vhosts/gametracker.smshosting.bg/www/fonts/';
$img = '/var/www/vhosts/gametracker.smshosting.bg/www/images/bg' . $_POST['img'] . '.png';

require '../../includes/core.php';

$server_addr = $_SESSION['ip'] . ':' . $_SESSION['port'];

$ip_port = explode(':', $server_addr);

$ip = $ip_port[0];
$port = $ip_port[1];

$name = $result['name'];
$map = $result['map'];

ob_start();

$im = imagecreatefrompng($img);


imagettftext($im, trim($_POST['size_hostname']), 0, 265, 33, imagecolorallocate($im, $colors[$_POST['color_hostname']][2],  $colors[$_POST['color_hostname']][3],  $colors[$_POST['color_hostname']][4]), $font_path . trim($_POST['font_hostname']), $name);
imagettftext($im, trim($_POST['size_addr']), 0, 265, 58, imagecolorallocate($im, $colors[$_POST['color_addr']][2],  $colors[$_POST['color_addr']][3],  $colors[$_POST['color_addr']][4]), $font_path . trim($_POST['font_addr']), strval($ip) . ':' . strval($port));
imagettftext($im, trim($_POST['size_players']), 0, 488, 58, imagecolorallocate($im, $colors[$_POST['color_players']][2],  $colors[$_POST['color_players']][3],  $colors[$_POST['color_players']][4]), $font_path . trim($_POST['font_players']), strval($result["activeplayers"]) . '/' . strval($result["maxplayers"]));
imagettftext($im, trim($_POST['size_map']), 0, 360, 80, imagecolorallocate($im, $colors[$_POST['color_map']][2],  $colors[$_POST['color_map']][3],  $colors[$_POST['color_map']][4]), $font_path . trim($_POST['font_map']), $map);

if ($server->connected) {
	imagettftext($im, trim($_POST['size_status']), 0, 420, 33, imagecolorallocate($im, $colors[$_POST['color_status']][2],  $colors[$_POST['color_status']][3],  $colors[$_POST['color_status']][4]), $font_path . trim($_POST['font_status']), '0nline');
}
else {
	imagettftext($im, trim($_POST['size_status']), 0, 240, 33, imagecolorallocate($im, $colors[$_POST['color_status']][2],  $colors[$_POST['color_status']][3],  $colors[$_POST['color_status']][4]), $font_path . trim($_POST['font_status']), 'Offline');
}

imagesavealpha($im, true);

if (!ob_get_length() && imagepng($im, null, 9) && imagedestroy($im)) {
	$image_data = ob_get_clean();
	
	echo '<img src="data:image/png;base64,' . base64_encode($image_data) . '" alt="" />';
}
?>
