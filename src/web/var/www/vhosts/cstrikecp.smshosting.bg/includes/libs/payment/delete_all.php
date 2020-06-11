<?php
require 'class.Settings.php';
require 'class.PayPalButtonManager.php';
header('Content-Type: text/plain; charset=utf-8');
$ppbm = new PayPalButtonManager;
$buttons = $ppbm->search(strtotime('-1 year'));
foreach ($buttons as $button) {
	echo 'delete ', $button['HOSTEDBUTTONID'], '...';
	flush();
	$ppbm->delete($button['HOSTEDBUTTONID']);
	echo ' done', PHP_EOL;
	flush();
}
exit;
?>