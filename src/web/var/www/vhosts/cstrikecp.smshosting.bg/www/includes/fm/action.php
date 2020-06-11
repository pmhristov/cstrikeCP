<?php

/**
 * This code is part of the FileManager software (www.gerd-tentler.de/tools/filemanager), copyright by
 * Gerd Tentler. Obtain permission before selling this code or hosting it on a commercial website or
 * redistributing it over the Internet or in any other medium. In all cases copyright must remain intact.
 */

include_once('class/FileManager.php');

$container = isset($_REQUEST['fmContainer']) ? $_REQUEST['fmContainer'] : '';

if($container != '' && isset($_SESSION[$container])) {
	$FileManager = unserialize($_SESSION[$container]);
	$fmMode = isset($_REQUEST['fmMode']) ? $_REQUEST['fmMode'] : '';
	$fmName = isset($_REQUEST['fmName']) ? $_REQUEST['fmName'] : '';
	$fmRememberPwd = isset($_REQUEST['fmRememberPwd']) ? $_REQUEST['fmRememberPwd'] : '';

	if($fmMode == 'login' && $fmName != '' && $fmRememberPwd) {
		@setcookie($FileManager->container . 'LoginPwd', $fmName, time() + 90 * 24 * 3600);
	}

	if(!in_array($fmMode, $FileManager->binaryModes)) {
		if($FileManager->locale) @setlocale(LC_ALL, $FileManager->locale);
		header("Content-Type: text/html; charset=UTF-8");
		header('Cache-Control: private, no-cache, must-revalidate');
		header('Expires: 0');
		header('X-Robots-Tag: noindex, nofollow');
	}
	ob_start();
	$FileManager->action();
	ob_end_flush();
}
else {
	header('X-Robots-Tag: noindex, nofollow');
	$msg = 'Cannot restore FileManager object from PHP session - ';
	if($container == '') $msg .= 'fmContainer not set!';
	else if(!session_id()) $msg .= 'could not create session!';
	else $msg .= "\$_SESSION['$container'] not found!";
	FileManager::error($msg);
}

?>