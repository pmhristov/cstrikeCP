<?php
if(session_id() == '' || !isset($_SESSION)) {
    session_start();
}

require_once 'vendor/autoload.php';
use phpseclib\Crypt\RSA; 
use phpseclib\Net\SSH2; 
use phpseclib\Net\SFTP; 

if (isset($_SESSION['id'])) {
	$ssh_ip = $nodes[$_SESSION['node_id']]['ipaddress'];

	$key = new RSA();
	$key->loadKey(file_get_contents(INCLUDE_DIR . 'ssh/' . $nodes[$_SESSION['node_id']]['privatekey']));
	
	$login_sftp = new SFTP($ssh_ip, $nodes[$_SESSION['node_id']]['port']);
	$login_sftp->login($nodes[$_SESSION['node_id']]['username'], $key);
	
	$login_ssh2 = new SSH2($ssh_ip, $nodes[$_SESSION['node_id']]['port']);
	$login_ssh2->login($nodes[$_SESSION['node_id']]['username'], $key);	
	$login_ssh2->setTimeout(60);

	if (!$login_ssh2) {
		echo 'Warning: Cant connect to SSH';
	}
}

if (isset($_SESSION['id'])) {
	$server_balance = $DB->GetOne('SELECT `balance` FROM `servers` WHERE `id` = ?', array($_SESSION['id']));
}

function cfgReload() {
	global $DB, $login_ssh2, $login_sftp, $sftp, $mode_plugins, $anticheat_labels, $zp_modes, $plugins_order;
	
	$zp_modes = array(9, 23, 25, 27);
	$server_data = $DB->GetRow('SELECT * FROM `servers` WHERE `id` = ?', array($_SESSION['id']));
	
	$admins = $DB->GetAll('SELECT * FROM `admins` WHERE `server_id` = ? AND (`expire` > CURDATE() OR `expire` = ?)', array($_SESSION['id'], '0000-00-00'));
	$redirects = $DB->GetAll('SELECT * FROM `redirects` WHERE `server_id` = ?', $_SESSION['id']);
	$maps = $DB->GetAll('SELECT * FROM `maplist` WHERE `server_id` = ? ORDER BY `id`', $_SESSION['id']);

	$login_sftp->delete('/home/servers/cstrike/' . $_SESSION['id'] . '/cstrike/addons/amxmodx/configs/plugins.ini');
	$login_sftp->delete('/home/servers/cstrike/' . $_SESSION['id'] . '/cstrike/addons/amxmodx/configs/plugins-zplague.ini');
	
	$statsx_check = $DB->GetOne('SELECT `plugin` FROM `plugins` WHERE `plugin` = ? AND `server_id` = ? AND `active` = ?', array('statsx_shell', $_SESSION['id'], 1));
	$amxwho_check = $DB->GetOne('SELECT `plugin` FROM `plugins` WHERE `plugin` = ? AND `server_id` = ? AND `active` = ?', array('amxwho', $_SESSION['id'], 1));
	$galileo_check = $DB->GetOne('SELECT `plugin` FROM `plugins` WHERE `plugin` = ? AND `server_id` = ? AND `active` = ?', array('galileo', $_SESSION['id'], 1));
	$bbcommandsmenu_check = $DB->GetOne('SELECT `plugin` FROM `plugins` WHERE `plugin` = ? AND `server_id` = ? AND `active` = ?', array('bb_commands_menu', $_SESSION['id'], 1));
	$gag_check = $DB->GetCol('SELECT `plugin` FROM `plugins` WHERE `server_id` = ? AND `plugin` IN (\'amx_gag\', \'player_gag\', \'amx_gag_retry\', \'amx_gagv149\', \'GagSystem\') AND `active` = ?', array($_SESSION['id'], 1));



	$plugins_out = [];
	$plugins_services = [];
	$plugins_zp = [];
	$plugins_standart = [];

	$plugins_in = $DB->GetCol('SELECT `plugin` FROM `plugins` WHERE `server_id` = ? AND `active` = ?', array($_SESSION['id'], 1));

	foreach ($plugins_in as $plugin_in) {
		$plugin_info = $DB->GetRow('SELECT * FROM `pluginlist` WHERE `name` = ?', array($plugin_in));
		$plugin_content = $DB->GetCol('SELECT `content` FROM `pluginlist_content` WHERE `plugin` = ?', array($plugin_in));
		if (in_array($_SESSION['mode'], $zp_modes)) {
			if ($plugin_info['category'] == 'zp') {
				foreach ($plugin_content as $plugin_content_label) {
					array_push($plugins_zp, $plugin_content_label);
				}
			}
			else {
				foreach ($plugin_content as $plugin_content_label) {
					array_push($plugins_standart, $plugin_content_label);
				}			
			}	
		}
		else{
			if ($plugin_info['category'] != 'zp') {
				foreach ($plugin_content as $plugin_content_label) {
					array_push($plugins_standart, $plugin_content_label);
				}
			}
		}
	}


	$plugins = array_merge($plugins_order[1], $plugins_standart);

	if ($_SESSION['amx_bans'] == 1) {
		array_unshift($plugins, 'amxbans_core', 'amxbans_main');
		if(($key = array_search('admin', $plugins)) !== false) {
			unset($plugins[$key]);
		}
		if(($key = array_search('admincmd', $plugins)) !== false) {
			unset($plugins[$key]);
		}	
	}

	if (!empty($statsx_check)) {
		if(($key = array_search('statsx', $plugins)) !== false) {
			unset($plugins[$key]);
		}
	}

	if (!empty($galileo_check)) {
		if(($key = array_search('nextmap', $plugins)) !== false) {
			unset($plugins[$key]);
		}
		if(($key = array_search('mapchooser', $plugins)) !== false) {
			unset($plugins[$key]);
		}	
	}

	if (!empty($gag_check)) {
		array_push($plugins, $gag_check);
	}



	foreach ($plugins_order as $plugin_order) {
		foreach($plugins as $line) {
			if (in_array($line, $plugin_order)) {
				$plugins_out[] = $line;
			}
		}
	}

	if (!empty($redirects)) {
		array_push($plugins_services, 'xredirect');
	}

	if ($_SESSION['radio'] == 1) {
		array_push($plugins_services, 'online_radio');
	}

	if ($_SESSION['gamemenu'] == 1) {
		array_push($plugins_services, 'gamemenu');
	}	

	if ($_SESSION['antihlbrute'] == 1) {
		array_push($plugins_services, 'antihlbrute');
	}
	
	$anticheats = $DB->GetAll('SELECT `anticheat` FROM `anticheat` WHERE `server_id` = ? AND `active` = ?', array($_SESSION['id'], 1));
	$login_ssh2->exec('/usr/local/cstrike/anticheat.sh ' . $_SESSION['id'] . ' 0');
	$anticheat_plugins = [];
	foreach ($anticheats as $anticheat) {
		foreach ($anticheat_labels[$anticheat['anticheat']][3] as $anticheat_plugin_content) {
			array_push($anticheat_plugins, $anticheat_plugin_content);
			$login_ssh2->exec('/usr/local/cstrike/anticheat.sh ' . $_SESSION['id'] . ' 1 ' . $anticheat['anticheat']);
		}
	}
	
	
	$plugins = array_merge($plugins_out, array_diff($plugins_standart, $plugins_out), $anticheat_plugins, $mode_plugins[$_SESSION['mode']], $plugins_services);
	$plugin_text_fwrite = '';

	foreach ($plugins as $plugin_text) {
		$plugin_text_fwrite .= $plugin_text . '.amxx' . "\n";
	}
	
	$login_sftp->put('/home/servers/cstrike/' . $_SESSION['id'] . '/cstrike/addons/amxmodx/configs/plugins.ini', $plugin_text_fwrite);

	if (in_array($_SESSION['mode'], $zp_modes)) {
		$plugins_zp_text = '';
		foreach ($plugins_zp as $plugin_zp_text) {
			$plugin_zp_text_fwrite .= $plugin_zp_text . '.amxx' . "\n";
		}
			$login_sftp->put('/home/servers/cstrike/' . $_SESSION['id'] . '/cstrike/addons/amxmodx/configs/plugins-zplague.ini', $plugin_zp_text_fwrite);
	}	
	
	if (!empty($redirects)) {
		$login_sftp->delete('/home/servers/cstrike/' . $_SESSION['id'] . '/cstrike/addons/amxmodx/configs/serverlist.ini');
		foreach ($redirects as $redirect) {
			$addr_split = split(':', $redirect['addr']);
			$login_sftp->put('/home/servers/cstrike/' . $_SESSION['id'] . '/cstrike/addons/amxmodx/configs/serverlist.ini', '[' . $redirect['hostname'] . ']' . "\n" . 'address=' . $addr_split[0] . "\n" . 'port=' . $addr_split[1] . "\n", NET_SFTP_RESUME);
		}
	}

	if (!empty($admins) && $server_data['admins_custom'] == 0) {
		$login_sftp->delete('/home/servers/cstrike/' . $_SESSION['id'] . '/cstrike/addons/amxmodx/configs/users.ini');
		foreach ($admins as $admin) {
			$login_sftp->put('/home/servers/cstrike/' . $_SESSION['id'] . '/cstrike/addons/amxmodx/configs/users.ini', '"' . $admin['name'] . '" "' . $admin['passwd'] . '" "' . $admin['flags'] . '" "' . $admin['type'] . '"' . "\n", NET_SFTP_RESUME);
		}
	}
	
	if ($_SESSION['radio'] == 1) {
		$radio_servers = $DB->GetAll('SELECT * FROM `radio` WHERE `server_id` = ?', array($_SESSION['id']));
		$login_sftp->delete('/home/servers/cstrike/' . $_SESSION['id'] . '/cstrike/addons/amxmodx/configs/radio_list.ini');
		foreach ($radio_servers as $radio_server) {
			$login_sftp->put('/home/servers/cstrike/' . $_SESSION['id'] . '/cstrike/addons/amxmodx/configs/radio_list.ini', '"' . $radio_server['name'] . '" "' . $radio_server['addr'] . '"' . "\n", NET_SFTP_RESUME);			
		}
	}

	if ($_SESSION['gamemenu'] == 1) {
		$login_sftp->delete('/home/servers/cstrike/' . $_SESSION['id'] . '/cstrike/addons/amxmodx/configs/gamemenu.txt');
		$login_sftp->put('/home/servers/cstrike/' . $_SESSION['id'] . '/cstrike/addons/amxmodx/configs/gamemenu.txt', '"GameMenu" { "1" { "label" "#GameUI_GameMenu_ResumeGame" "command" "ResumeGame" "OnlyInGame" "1" } "2" { "label" "#GameUI_GameMenu_Disconnect" "command" "Disconnect" "OnlyInGame" "1" "notsingle" "1" } "4" { "label" "#GameUI_GameMenu_PlayerList" "command" "OpenPlayerListDialog" "OnlyInGame" "1" "notsingle" "1" } "5" { "label" "" "command" "" "OnlyInGame" "1" } "6" { "label" "' . $_SESSION['hostname'] . '" "command" "engine CONNECT ' . $_SESSION['ip'] . ':' . $_SESSION['port'] . '" } "7" { "label" "" "command" "" } "8" { "label" "#GameUI_GameMenu_NewGame" "command" "OpenCreateMultiplayerGameDialog" } "9" { "label" "#GameUI_GameMenu_FindServers" "command" "OpenServerBrowser" } "10" { "label" "#GameUI_GameMenu_Options" "command" "OpenOptionsDialog" } "11" { "label" "#GameUI_GameMenu_Quit" "command" "Quit" } }' . "\n", NET_SFTP_RESUME);	
	}	
	
	if ($_SESSION['amx_bans'] == 1) {
		$login_sftp->delete('/home/servers/cstrike/' . $_SESSION['id'] . '/cstrike/addons/amxmodx/configs/sql.cfg');
		$login_sftp->put('/home/servers/cstrike/' . $_SESSION['id'] . '/cstrike/addons/amxmodx/configs/sql.cfg', 'amx_sql_host    "' . $_SESSION['amx_bans_host'] . '"
amx_sql_user    "' . $_SESSION['amx_bans_dbuser'] . '"
amx_sql_pass	"' . $_SESSION['amx_bans_dbpasswd'] . '"
amx_sql_db  	"' . $_SESSION['amx_bans_db'] . '"
amx_sql_table   "admins"
amx_sql_type    "mysql"' . "\n", NET_SFTP_RESUME);	
	}
	

	
 	if (!empty($_SESSION['motd'])) {
		$login_sftp->delete('/home/servers/cstrike/' . $_SESSION['id'] . '/cstrike/motd.txt');
		$login_sftp->put('/home/servers/cstrike/' . $_SESSION['id'] . '/cstrike/motd.txt', $_SESSION['motd'] . "\n", NET_SFTP_RESUME);
	}
	
	
	if (!empty($maps)) {
		$login_sftp->delete('/home/servers/cstrike/' . $_SESSION['id'] . '/cstrike/mapcycle.txt');
		foreach ($maps as $map_val) {				
			$login_sftp->put('/home/servers/cstrike/' . $_SESSION['id'] . '/cstrike/mapcycle.txt', $map_val['map'] . "\n", NET_SFTP_RESUME);
		}
	}
	
	if ($_SESSION['ftp'] > 0) {
		$login_ssh2->exec('/usr/local/cstrike/ftp.sh ' . $_SESSION['id'] . ' ' . escapeshellarg(crypt($_SESSION['passwd_cp'], '')) . ' ' . $_SESSION['ftp']);
	}	
}

function cfgRestart() {
	global $server;

	$server->RconCommand('say Server Restart after 3 seconds');
	sleep(1);
	$server->RconCommand('say Server Restart after 2 seconds');
	sleep(1);
	$server->RconCommand('say Server Restart after 1 second');
	sleep(1);
	$server->RconCommand('restart');
}

function serverRestart() {
	global $login_ssh2;
	$login_ssh2->exec('/usr/local/cstrike/restart.sh ' . $_SESSION['id']);
}

function isValidPasswd($passwd) {
	if (!preg_match('/^(?=.*\d)(?=.*[A-Za-z])[0-9A-Za-z]{6,50}$/', $passwd)) {
		return false;
	}
	else {
		return true;
	}
}


#Valid HOSTNAME
function isValidHostname($hostname = '') {
if ( strlen($hostname) == 0 ) {
die('Грешка: полето \'HOSTNAME\' не трябва да е празно !');
}
if ( strlen($hostname) < 3 || strlen($hostname) > 35 ) {
	die('Грешка: дължината на полето \'HOSTNAME\' не може да бъде по-малка от 3 и по-голяма от 35 символа !');
}
  $return = true;
  $str_valid_chars = 'abcdefghijklmnopqrstuvwxyz';
  $str_valid_chars .= strtoupper($str_valid_chars);
  $str_valid_chars .= '1234567890-+=:.{}()@[]~#><* ';
  $arr_valid_chars = str_split($str_valid_chars);
  $arr_hostname = str_split($hostname);
  foreach($arr_hostname as $char_hostname) {
    if (!in_array($char_hostname, $arr_valid_chars)) {
     $return = false;
	 die('Грешка: използвали сте невалидни символи в полето \'HOSTNAME\'. Позволените символи са [a-z] [A-Z] [1-9] -=:.{}()@[]~#><* ');
    }
  }
  return $return;
}

function isValidValue($value = '') {
	$return = true;
	$str_valid_chars = '1234567890,.';
	$arr_valid_chars = str_split($str_valid_chars);
	$arr_value = str_split($value);
	foreach($arr_value as $char_value) {
		if (!in_array($char_value, $arr_valid_chars)) {
			$msg = 'Невалидна сума.';
			$return = false;
		}
	}
	return $return;
}

function fixDate($date, $long = false) {
	if ($date !== trim($date, '1..9')) {
		return strftime(($long ? '%d %B %Y' : '%d.%m.%Y'), is_int($date) ? $date : strtotime($date));
	}
	else {
		return '[няма данни]';
	}
}

#Mobio.BG SMS Check
function checkSmsCode($servID, $code, $do_not_mark = false) {
	$GLOBALS['LAST_MOBIO_RESP_NUM'] = $mobio_response = file_get_contents('http://www.mobio.bg/code/checkcode.php?servID=' . $servID .'&code=' . $code . ($do_not_mark ? '&do_not_mark=1&num=1' : '&num=1'));
	$mobio_response = explode(':', $mobio_response);
	$GLOBALS['MOBIO_NUMBER'] = $mobio_response[1];
	return ((!empty($mobio_response[0])) && (0 === strpos('PAYBG=OK', $mobio_response[0])));
}


function AddDate($date = DATE, $days = null) {
	$nowdate = date('Y-m-j', strtotime('+' . $days .  ' day', strtotime($date)));
	return $nowdate;
}

function dateAdd($date = DATE, $whole_month = false, $days = 31) {
	$days_of_month = cal_days_in_month(CAL_GREGORIAN, date('n'), date('o'));
	
	if ($whole_month == false) {
		$nowdate = date('Y-m-j', strtotime('+' . $days .  ' day', strtotime($date)));
	}
	else {
		$nowdate = date('Y-m-j', strtotime('+' . $days_of_month .  ' day', strtotime($date)));
	}
	
	return $nowdate;
}

function send_email($to = 'office@smshosting.bg', $subject = '<no subject>', $body = 'no contents', $html = false) {
	require_once(INCLUDE_LIB_DIR . 'class.gmail_phpmailer.php');

	$mail = new PHPMailer();
	/* $mail->SMTPDebug=4; */
  	$mail->IsSMTP();
	$mail->SMTPSecure = 'ssl';	
	$mail->CharSet = 'utf-8';
	$mail->Host = SMTP_HOST;
	$mail->Port = SMTP_PORT;
	
	$mail->Username = SMTP_USER;
	$mail->Password = SMTP_PASS;
	
	$mail->From = SMTP_FROM;
	$mail->FromName = 'СМСХостинг.БГ';
	$mail->AddReplyTo('office@smshosting.bg', 'СМСХостинг.БГ');
	$mail->Subject = $subject;

	if ($html) {
		$mail->MsgHTML($body);
	}
	else {
		$mail->Body = $body;
	}

	if (is_array($to)) {
		foreach ($to as $email) {
			$mail->AddAddress($email);
		}
	}
	else {
		$mail->AddAddress($to);
	}

	return $mail->Send();
}

function random_gen($length) {
  $random = '';
  srand((double)microtime()*1000000);
  $char_list = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
  $char_list .= "abcdefghijklmnopqrstuvwxyz";
  $char_list .= "1234567890";
  // Add the special characters to $char_list if needed

  for($i = 0; $i < $length; $i++)
  {
    $random .= substr($char_list,(rand()%(strlen($char_list))), 1);
  }
  return $random;
}

function checkOrder($type = null, $service = null, $service_id = null, $mail = null) {
	global $DB;
	$order_check = $DB->GetRow('SELECT * FROM `orders` WHERE `type` = ? AND `service` = ? AND `service_id` = ? AND `mail` = ?', array($type, $service, $service_id, $mail));
	if (!empty($order_check)) {
		return false;
	}
	return true;
}

function send_email_payment($to = 'noreply@smshosting.bg', $subject = '<no subject>', $payment_type = '', $price = null, $html = false) {
	require_once(INCLUDE_LIB_DIR . 'class.gmail_phpmailer.php');

	switch ($payment_type) {
		case 'cash':
			$body = '<h2>Вашата заявка беше успешно приета</h2>
			За да продължите с активацията на вашата услуга изпълнете следните стъпки:<br /><br />
			<b>1. </b> Изпратете ни посочената сума в най - близкия банков клон или чрез онлайн банкиране. Нашите данни са: 
			<p>
			<i>Банка:</i> <b>Първа инвестиционна (FiBank)</b><br />
			<i>IBAN:</i> <b>BG53FINV91501015703341</b><br />
			<i>BIC:</i> <b>FINVBGSF</b><br />
			<i>Получател:</i> <b>Нова Хост ЕООД</b><br />
			<i>Относно (Основание):</i> <b>' . $subject . '</b><br />
			<i>Сума:</i> <b>' . $price . '</b> лв. с ДДС
			</p>
			<i>Важно:</i> Имаите предвид че ако изпратите сумата от друга банка тя може да пристигне със закъснение. Ако желаете можете да ни прикачите снимка на вашето платежно (само при превод от банков клон) и по този начин няма да е нужно да чакате докато превода пристигне при нас.
			<p>
			<b>2.</b> Изпратете ни писмо до <a href="mailto:sales@smshosting.bg">sales@smshosting.bg</a> в което описвате че сте направили превод до нас като задължително трябва опишете името от което е изпратен превода и с каква цел (за коя услуга) е изпратен. 
			</p>
			<p>
			<b>3.</b> Остава единствено да проявите търпение докато ние прегледаме вашата заявка. В момента в който сумата пристигне при нас ще активираме вашата услуга. След като активираме вашата услуга ще получите писмо с данни и инструкции за поръчаната от вас услуга.
			</p>
			
			<b>Благодарим ви че избрахте услугите на СМСХостинг.БГ</b><br />
		<small>(Това е автоматично генерирано съобщение, моля не отговаряйте!)</small>';
		break;	
		case 'easypay':
			$body = '<h2>Вашата заявка беше успешно приета</h2>
			За да продължите с активацията на вашата услуга изпълнете следните стъпки:<br /><br />
			Необходимо е да посетите най-удобния за вас клон на EasyPay.<br />
При заплащането на поръчката трябва задължително за основание за плащане да посочите 10-цифрения код за плащане, който е уникален за конкретната поръчка. Този код се генерира от системата на EasyPay и се визуализира при самата поръчка. След като заплатите услугата служител на EasyPay ще я активира и вие ще получите данните на посочения мейл адрес.<br />

			<p>
			<i>Код за плащане:</i> <b>' . $price . '</b><br />
			<b>Кода е валиден 48 часа след неговото генериране!</b>
			</p>

			<b>Благодарим ви че избрахте услугите на СМСХостинг.БГ</b><br />
		<small>(Това е автоматично генерирано съобщение, моля не отговаряйте!)</small>';
		break;
		case 'ebg':
			$body = '<h2>Вашата заявка беше успешно приета</h2>
			За да продължите с активацията на вашата услуга изпълнете следните стъпки:<br /><br />
			<b>1. </b> Изпратете ни посочената сума чрез онлайн системата на <a href="http://www.epay.bg">ePay.BG</a> или от офис на <a href="http://www.easypay.bg">EasyPay</a>. Нашите данни са: 
			<p>
			<i>Клиентски номер (КИН):</i> <b>69821</b><br />
			<i>Е-Майл:</i> <b>pay@smshosting.bg</b><br />
			<i>Относно (Основание):</i> <b>' . $subject . '</b><br />
			<i>Сума:</i> <b>' . $price . '</b> лв. с ДДС
			</p>
			<p>
			<b>2.</b> Изпратете ни писмо до <a href="mailto:sales@smshosting.bg">sales@smshosting.bg</a> в което описвате че сте направили превод до нас като задължително трябва опишете клиентския номер (КИН) или името от което е заплатена услугата както и основанието за самия превод.
			</p>
			<p>
			<b>3.</b> Остава единствено да проявите търпение докато ние прегледаме вашата заявка. В момента в който сумата пристигне при нас ще активираме вашата услуга. След като активираме вашата услуга ще получите писмо с данни и инструкции за поръчаната от вас услуга.
			</p>
			<b>Благодарим ви че избрахте услугите на СМСХостинг.БГ</b><br />
		<small>(Това е автоматично генерирано съобщение, моля не отговаряйте!)</small>';
		break;
		case 'post':
			$body = '<h2>Вашата заявка беше успешно приета</h2>
			За да продължите с активацията на вашата услуга изпълнете следните стъпки:<br /><br />
			<b>1. </b> Изпратете ни посочената сума чрез пощенски или телеграфен запис в най - близкия пощенски офис. Запи Нашите данни са: 
			<p>
			<i>Получател:</i> <b>Петър Мирославов Христов</b><br />
			<i>Адрес:</i> <b>5600 Троян, До поискване</b><br />
			<i>Относно:</i> <b>' . $subject . '</b><br />
			<i>Сума:</i> <b>' . $price . '</b> лв. с ДДС
			</p>
			<i>Важно:</i> Имаите предвид че сумата ще пристигне със закъснение (от 1 до 3 дни). Ако желаете можете да ни прикачите снимка на пощенската разписка и по този начин няма да е нужно да чакате докато записа пристигне при нас.
			<p>
			<b>2.</b> Изпратете ни писмо до <a href="mailto:sales@smshosting.bg">sales@smshosting.bg</a> в което описвате че сте направили превод до нас като задължително трябва опишете името от което е изпратен превода и с каква цел (за коя услуга) е изпратен.
			</p>
			<p>
			<b>3.</b> Остава единствено да проявите търпение докато ние прегледаме вашата заявка. В момента в който сумата пристигне при нас ще активираме вашата услуга. След като активираме вашата услуга ще получите писмо с данни и инструкции за поръчаната от вас услуга.
			</p>
			<b>Благодарим ви че избрахте услугите на СМСХостинг.БГ</b><br />
		<small>(Това е автоматично генерирано съобщение, моля не отговаряйте!)</small>';
		break;		
	}
	
	$mail = new Gmail_PHPMailer();
	/* $mail->SMTPDebug=4; */
	$mail->CharSet = 'utf-8';
	
	$mail->FromName = 'noreply@smshosting.bg';
	$mail->AddReplyTo('noreply@smshosting.bg', 'noreply@smshosting.bg');
	$mail->Subject = $subject;
	
	if ($html) {
		$mail->MsgHTML($body);
	}
	else {
		$mail->Body = $body;
	}
	
	if (is_array($to)) {
		foreach ($to as $email) {
			$mail->AddAddress($email);
		}
	}
	else {
		$mail->AddAddress($to);
	}

	return $mail->Send();
}

function serviceCharge($service = null, $service_name = null, $server_id = null, $value = null, $check = false) {
	global $DB, $server_balance;
	
	if ($check == true) {
		if ($_SESSION['balance'] - $value < 0) {
			return false;
		}
		else {
			return true;
		}
	}
	else {
		$calculated_value = number_format($server_balance - $value, 2, '.', ' ');
 		$DB->Execute('UPDATE `servers` SET `balance` = ? WHERE `id` = ?', array($calculated_value, $server_id));
		$balance = $DB->GetOne('SELECT `balance` FROM `servers` WHERE `id` = ?', array($server_id));
		$_SESSION['balance'] = $balance;
		
		$balance_table = 'balance';
		$balance_records = array(
			'type' => 'charge',
			'service' => $service,
			'service_name' => $service_name,
			'server_id' => $server_id,
			'value' => number_format($value, 2, '.', ' ')
		);	
		$DB->AutoExecute($balance_table, $balance_records, 'INSERT');
		
		return true;
	}
}


function addBalance($server_id, $value = null, $payment_type = null) {
	
	global $DB;
	
	$srv_info = $DB->GetRow('SELECT * FROM `servers` WHERE `id` = ?', array($server_id));
	$calculated_value = number_format($srv_info['balance'] + $value, 2, '.', ' ');
	$DB->Execute('UPDATE `servers` SET `balance` = ? WHERE `id` = ?', array($calculated_value, $srv_info['id']));
	$balance = $DB->GetOne('SELECT `balance` FROM `servers` WHERE `id` = ?', array($srv_info['id']));
	
	$balance_table = 'balance';
	$balance_records = array(
		'type' => 'add',
		'payment_method' => $payment_type,
		'server_id' => $srv_info['id'],
		'value' => $value,
	);	
	$DB->AutoExecute($balance_table, $balance_records, 'INSERT');
}

function CalculateDate($days = null) {
	$date = date('Y-m-j');
	$nowdate = date('Y-m-j', strtotime('+' . $days .  ' day', strtotime($date)));
	return $nowdate;
}

function getNextEpayInvoiceId() {
	global $DB_PAYMENT;
	$return = false;	
	$DB_PAYMENT->Execute('LOCK TABLES `variables` WRITE');
	$rs = $DB_PAYMENT->GetOne('SELECT `value` FROM `variables` WHERE `name` = "epay_next_invoice_id"');
	
	do {
		if (!$rs) {
			break;
		}
		$return = $rs;
		$DB_PAYMENT->Execute('UPDATE `variables` SET `value` = `value` + 1 WHERE `name` = "epay_next_invoice_id"');
	}
	while (false);
	
	$DB_PAYMENT->Execute('UNLOCK TABLES');
	
	return $return;
}

?>