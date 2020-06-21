<?php

$action_table = 'actions';
//$fonts_dir = '/var/www/vhosts/gametracker.smshosting.bg/www/fonts';
$colors = array(
	'black' => array('black', 'Черен', 0, 0, 0),
	'red' => array('red', 'Червен', 255, 0, 0),
	'blue' => array('blue', 'Син', 0, 0, 255),
	'green' => array('green', 'Зелен', 0, 255, 0),
	'yellow' => array('yellow', 'Жълт', 219,219,112),
	'white' => array('white', 'Бял', 237,237,237),
	'purple' => array('purple', 'Лилав', 128, 0, 128),
	'pink' => array('pink', 'Розов', 255, 0, 255),
	'orange' => array('orange', 'Оранжев', 255, 69, 0),
);

$game_type = array(
	'hlds' => 'Counter-Strike 1.6',
	'srcds' => 'Counter-Strike Source'
);

$ips = array(
	'79.124.67.162',
	'79.124.67.163',
	'79.124.67.164',
	'79.124.67.165',
	'79.124.67.166',
	'79.124.67.167',
	'79.124.67.168',
	'79.124.67.169',
	'79.124.67.170',
	'79.124.67.171',
	'79.124.67.172',
	'79.124.67.173',
	'79.124.67.174',
	'78.128.6.2',
	'78.128.6.3',
	'78.128.6.4',
	'78.128.6.5',
	'78.128.6.6',
	'78.128.6.7',
	'78.128.6.8',
	'78.128.6.9',
	'78.128.6.10',
	'78.128.6.11',
	'78.128.6.12',
	'78.128.6.13',
	'78.128.6.14',
);

$ports = array(
	'27015',
	'27016',
	'27017',
	'27018',
	'27019',
	'27020',
	'27021',
	'27022',
);

$ssh_details = array(
	1 => array(1, 104, '79.124.67.162'),
	2 => array(105, 208, '78.128.6.2'),
);

$ftp_hosts = array(
	1 => array(1, 104, 'csftp.smshosting.bg'),
	2 => array(105, 208, 'csftp.smshosting.bg'),
);

$plan_labels = array(
	1 => array('CS 10'),
	2 => array('CS 18'),
	3 => array('CS 24'),
	4 => array('CS 32'),
);


$mode_labels = array(
	2 => array(2, 'AmXMoDX Classic [1.8.3-reHLDS]', '2.40'),
	/* 3 => array(3, 'War3FT [3.0 RC13]', '2.40'), */
	4 => array(4, 'DeathMatch Respawn (CSDM) [ReCSDM 3.6]', '2.40'),
	/* 5 => array(5, 'DeathMatch Respawn (CSDM) + War3 мод [2.1.2 + 3.0 RC13]', '3.60'), */
	6 => array(6, 'Hide N Seek (HNS) [2.8]', '3.60'),
	7 => array(7, 'DeathRun [3.0.3]', '3.60'),
	8 => array(8, 'Jump (ProKreedz) [2.00]', '3.60'),
	9 => array(9, 'Zombie Plague [4.3 Fix 5a]', '4.80'),
	10 => array(10, 'SuperHero [1.2.0.14]', '4.80'),
	11 => array(11, 'GunGame [2.13]', '2.40'),
	12 => array(12, 'Hide N Seek (HNS) Training [2.8]', '2.40'),
	13 => array(13, 'Zombie Base Builder [6.5]', '4.80'),
	14 => array(14, 'Jail Break Extreme [1.9]', '3.60'),
	15 => array(15, 'Hide N Seek (HNS) XP [2.8]', '3.60'),
	16 => array(16, 'Capture The Flag [3.1b]', '2.40'),
	17 => array(17, 'Catch mode [2.0.1 Black Edition]', '2.40'),
	18 => array(18, 'Snowball War [3.0.5]', '2.40'),
	19 => array(19, 'Soccer Jam [2.0.7a]',  '2.40'),
	20 => array(20, 'Knife Mode [1.0]',  '2.40'),
	21 => array(21, 'Block Maker [4.0.1]', '3.60'),
	22 => array(22, 'Paintball Mod [3.0.3]',  '2.40'),
	23 => array(23, 'Zombie Plague [5.0.5]', '4.80'),
	24 => array(24, 'Zombie Base Builder [Veco - 4.3]', '4.80'),
	25 => array(25, 'Zombie Plague Advance [1.6.1]', '4.80'),
/* 	26 => array(26, 'ShaDezz Course Maker [3.6]', '2.40'),  */
	27 => array(27, 'Zombie Plague [4.3 Fix 4]', '4.80'),
	28 => array(28, 'Catch Mode [3.3 Silver edition]', '2.40'),
	29 => array(29, 'BF2Rank [1.5.5]', '4.80'),
	30 => array(30, 'Volleyball Mod [1.1]', '2.40'),
	31 => array(31, 'Fun Mod', '2.40'),
	32 => array(32, 'WARZZZ [2.4]', '4.80'),
	33 => array(33, 'BF2Rank [1.5.5]+ DeathMatch Respawn (CSDM) [2.1.3]', '6.00'),
	34 => array(34, 'Furien V64 [64.2.6b]', '4.80'),
	35 => array(35, 'AWP War + Shop + Admin Menu', '3.60'),
);

$mode_description = array(
	1 => 'Сървъра е изчистен от всякакви видове модове и плъгини.',
	2 => 'Това е класически amxmodx мод с харектерните за него плъгини',
	3 => 'Играта Warcraft 3 се пренася като мод във вашия сървър като ви позволява да избирате различна раса като всяка една има свои уникални умения.',
	4 => 'Мода е характересен за масовите сървъри. Всеки път когато играч умре той се ражда веднага и то на различно място на картата. Парите не са важни тъй като всеки може да купи каквото пожелае оръжие в отделно меню което не изисква пари и то само с едно копче.',
	5 => 'Смесен мод от предишните два. Мода е много олекотен и наистина си заслужава да опитате особено ако държите вашия сървър да е пълен с играчи и да запазите техния интерес към сървъра.',
	6 => 'Мода все още няма въведено описание',
	7 => 'Класически 4fun Deathrun мод',
	8 => 'Мода все още няма въведено описание',
	9 => 'Класически Zombie Plague мод',
	10 => 'Мода все още няма въведено описание',
	11 => 'Мода все още няма въведено описание',
	12 => 'Мода все още няма въведено описание',
	13 => 'Мода все още няма въведено описание',
	14 => 'Мода все още няма въведено описание',
	15 => 'Мода все още няма въведено описание',
	16 => 'Мода все още няма въведено описание',
	17 => 'Мода представлява типичната игра на гоненица което ви ви позволява да тичате много по бързо с по - ниска гравитация, много повече забавление и ... Има 2 отбора като всеки от тях трябва да хване другия. Единия е отбора на "бягащите" а другия е отбора на "гонещите". Отборите се сменят всеки рунд.',
	18 => 'Изключително желан мод около коледните празници и снежните дни. Мода представява война със снежни топки като гранатата е заменена със снежна топка и трябва да уцелите другия. Повече информация можете да прочете на адрес http://cs-bg.info/plugin/550/',
	19 => 'Това е точно като футбол. Който от двата отбора направи 15 гола - печели.
Има ъпгрейди като например: Stamina ( увеличава издържливостта ви ), Strength (Увеличава силата на ритане), Agility (По-голяма бързина) , Dexterity (Увеличава възможността да хванете топката ), Disarm (Увеличава възможността да отнемете на някого топката). Повече информация можете да прочете на адрес http://cs-bg.info/plugin/375/',
	20 => 'Интересен мод който променя моделите на вашия нож като всеки нож си има специални умения. Повече информация за мода можете да прочетете на адрес http://cs-bg.info/plugin/873/',
	21 => 'Мода все още няма въведено описание. Повече информация можете да намерите на адрес http://forums.alliedmods.net/showthread.php?p=504608',
	22 => 'Това е мод, с който може да играете пайнтбал и в CS. Повече информация можете да прочетете на адрес <a href="http://cs-bg.info/plugin/461/">http://cs-bg.info/plugin/461/</a>',
	23 => 'Класически Zombie Plague мод 5.0.5',
	24 => 'Класически Zombie Base Builder с модификации от VeCo',
	25 => 'Усъвършенствана версия на Zombie Plague 4.3.',
/* 	26 => 'ShaDezz Course Maker 6.3',
 */	27 => 'Zombie Plague мод, но с по-стар фикс [4]',
	28 => 'Мода представлява типичната игра на гоненица което ви ви позволява да тичате много по бързо с по - ниска гравитация, много повече забавление и ... Има 2 отбора като всеки от тях трябва да хване другия. Единия е отбора на "бягащите" а другия е отбора на "гонещите". Отборите се сменят всеки рунд.',
	29 => 'Този плъгин е опит да се пресъздаде "ранкинг" системата от Battlefield в CS 1.6. Повече информация можете да намерите на адрес <a href="https://forums.alliedmods.net/showthread.php?p=442260" target="_blank">https://forums.alliedmods.net/showthread.php?p=442260</a>',
	30 => 'Това е мод, с който може да се играе волейбол. Двойките и единиците са с ръце и има топка, с която те трябва да играят волейбол. Ако тя падне на земята в полето на съответния отбор всички умират. Преди рунда отгоре на екрана се изписва името на играча, който ще изпълнява сервиза (началната топка) и се брои до 10. Повече информация можете да намерите на адрес <a href="http://cs-bg.info/plugin/1196/" target="_blank">http://cs-bg.info/plugin/1196/</a>',
	31 => 'Чрез този плъгин играта става по-забавна.
<br />
Настройките се правят в funmod.cfg, който се поставя в addons/amxmodx/configs/
<br />
Напишете в чата "/funmod" (без кавичките) и ще ви излезе меню със следните неща:
<br />
Invisibility - $10000 (невидимост)<br />
Gravity 200 - $2000 (гравитация - 200)<br />
Give me 500 hp - $8000 (дава ви 500 кръв)<br />
Make me glow - $1000 (прави ви така, че да светите)<br />
Enemy look - $16000 (вражески изглед)<br />
Buy NoClip - $13000 (купува ви прозрачност)',
	32 => '<a href="http://forums.alliedmods.net/showthread.php?t=185798">http://forums.alliedmods.net/showthread.php?t=185798</a>',
	33 => 'Този плъгин е опит да се пресъздаде "ранкинг" системата от Battlefield в CS 1.6. Повече информация можете да намерите на адрес <a href="https://forums.alliedmods.net/showthread.php?p=442260" target="_blank">https://forums.alliedmods.net/showthread.php?p=442260</a> Различното в тази версия е, че идва с вградена CSDM система.',
	34 => '<a href="https://forums.alliedmods.net/showthread.php?t=224430" target="_blank">https://forums.alliedmods.net/showthread.php?t=224430</a>',	
	35 => '<a href="https://forums.alliedmods.net/showthread.php?t=211721" target="_blank">https://forums.alliedmods.net/showthread.php?t=211721</a>',	

);

$mode_plugins = array(
	1 => array(),
	2 => array(),
	3 => array('war3ft'),
	4 => array(),
	5 => array('war3ft'),
	6 => array('hidenseek', 'frostnades'),
	7 => array('deathrunmanager', 'Deathrun_Shop', 'linux_func_rotating_fix_engine', 'stuck', 'DeathrunMapsFixer'),
	8 => array('prokreedz'),
	9 => array('zombie_plague40', 'zp_zclasses40'),
	10 => array(),
	11 => array('gungame'),
	12 => array('hns_training_2.8'),
	13 => array(),
	14 => array('jbextreme', 'jbextreme_es'),
	15 => array('hidenseek', 'frostnades', 'hns_xp'),	
	16 => array('GHW_CTF'),
	17 => array('catch_mod'),
	18 => array('snowball_war'),
	19 => array('soccerjam', 'soccerjam_t'),
	20 => array('knife_mod'),
	21 => array('blockmaker_v4.01'),
	22 => array('paintballnade', 'paintballmod', 'paintballgun'),
	23 => array(),
	24 => array('vzbb_mod', 'vzbb_addon_prep_time', 'vzbb_item_antidote', 'vzbb_item_battlebuild', 'vzbb_item_firenade', 'vzbb_item_frostnade', 'vzbb_item_healthkit', 'vzbb_item_nadeimmune', 'vzbb_item_protection', 'vzbb_item_scannade', 'vzbb_item_self_explosion', 'vzbb_item_shield', 'vzbb_item_toxic_mine', 'vzbb_item_warzzz_mine', 'vzbb_zclass_big', 'vzbb_zclass_fast', 'vzbb_zclass_jump', 'vzbb_zclass_regen', 'vzbb_zclass_vamp'),
	25 => array('zombie_plague_advance_v1-6-1', 'zpa_zclasses40', 'zp_game_mode_example'),
/* 	26 => array('frostnades', 'cashmod_fixed', 'sbm'),
 */	27 => array('zombie_plague40', 'zp_zclasses40'),
	28 => array('catch_silver_3_3'),
	29 => array('bf2rank'),
	30 => array('VolleyBallMod'),
	31 => array('funmod'),
	32 => array('warzzz'),
	33 => array('bf2rank'),
	34 => array('furien_v64'),
	35 => array('awpmod_en', 'awpmod_menu_en', 'awpmod_shop_en'),
);

$anticheat_labels = array(
	'' => array('Няма', '', '', array()),
	'csf' => array('CSF Anticheat 1.24c', 'csf', 'http://amxmodxbg.org/forum/viewtopic.php?t=24019', array('csf_anticheat')),
	'timepass' => array('TimePass Anticheat 9.0', 'timepass', 'http://cs-bg.info/forum/viewtopic.php?f=7&t=79596', array('TP_aim')),
	'ujac' => array('Ultimate Jump Anti Cheat 1.4.2', 'ujac', 'http://cs-bg.info/forum/viewtopic.php?f=8&t=135743', array('amx_version', 'mwheelupenforcer', 'ultimate_jump_anti_cheat')),
);

$payments_plans = array(
	1 => array('0.60', 1095, 5681),
	2 => array('1.20', 1093, 5661),
	3 => array('2.40', 1092, 5679),
	4 => array('4.80', 1094, 5680),
	5 => array('6.00', 1096, 10516),
);

$payments_names = array(
	'admin' => 'СМС администратор',
	'vip' => 'СМС VIP',
);

$plugins_categories = array(
	'security' => 'Сигурност',
	'fun' => 'Забава',
	'zp' => 'Zombie Plague',
	'gg' => 'Gun Game',
	'sh' => 'Super Hero',
	'ghost' => 'Ghost',
	'deathrun' => 'Deathrun',
	'vzbb' => 'VZBB',
	'undefined' => 'Без категория',
	'csdm' => 'Deathmatch',
	'jb' => 'Jail Break',
);

$prices_sms = array(2.40, 4.80, 6.00);
$prices = array(2.40, 4.80, 6.00, 8.00, 10.00);

$yes_no = array(
	1 => 'Да',
	0 => 'Не',
);

$sms_plans_details = array(
	'2.40' => array('2', 'sbg', 1092, '2.40', 5679),
	'4.80' => array('3', 'sbg', 1094, '4.80', 5680),
	'6.00' => array('4', 'sbg', 1096, '6.00', 10516),
);



$payment_labels = array(
	'sms' => 'СМС',
	'paypal' => 'чрез системата на <a href="https://www.paypal.com/bg" target="_blank">PayPal</a>',
	'cash' => 'По банков път',
	'epay' => 'чрез системата на <a href="https://www.epay.bg/" target="_blank">ePay.BG</a>',
	'easypay' => 'от офис на <a href="http://easypay.bg/" target="_blank">EasyPay</a>',
	'creditcard' => 'с кредитна карта',
	'ebg' => 'чрез системата на <a href="http://www.ebg.bg/" target="_blank">eBG.bg</a>',
	'post' => 'Пощенски запис',
);

$returnedData_msg = array(
	'new' => '<div style="text-align: center;"><h3>Вашата заявка е успешно изпълнена!</h3></div>',
	
	'renew' => '<div style="text-align: center;"><h3>Вашата заявка е успешно изпълнена!</h3></div>',
	
	'request' => '<div style="text-align: center;"><h3>Вашата заявка е успешно изпълнена!</h3></div>',

	'truesms' => '<div style="text-align: center;"><h3>Вашата заявка е успешно изпълнена!</h3></div>',

	'falsesms' => '<div style="text-align: center;"><h3>Вашата заявка е успешно изпълнена!</h3></div>',
	
	'paypal' => '<div style="text-align: center;"><h3>Вашата заявка е успешно изпълнена!</h3></div>',
);

$menus = array(
	'dashboard' => array('lastip'),
	'balance' => array('balance-current', 'balance-add'),
	'basic' => array('map', 'cmd', 'msg', 'cfgs', 'resetstats', 'kickban', 'sreset'),
	'administration' => array('mode', 'plugins', 'users', 'anticheat', 'hlxbans', 'ftp', 'fps', 'master', 'motd', 'scrollmsg', 'redirects', 'maplist', 'metamod', 'cvars', 'players', 'radio', 'gamemenu'),
	'settings' => array('hostname', 'rcon', 'svpasswd', 'passwdcp'),
	'extras' => array('gametracker'),
);

$paths = array(
	'dashboard' => array('Начално табло', 0),
	'players' => array('Активни играчи', 1, 'Основни'),
	'plan' => array('Промяна на план', 1, 'Основни'),
	'balance-current' => array('Текущ баланс', 1, 'Баланс'),
	'balance-add' => array('Зареждане на баланс', 1, 'Баланс'),
	'map' => array('Промяна на карта', 1, 'Контрол на сървъра'),
	'cmd' => array('Изпращане на команда', 1, 'Контрол на сървъра'),
	'msg' => array('Изпращане на съобщение', 1, 'Контрол на сървъра'),
	'mode' => array('Сървър мод', 1, 'Персонализация'),
	'plugins' => array('Добавки (плъгини)', 1, 'Персонализация'),
	//'players' => array('Игрални слотове', 1, 'Персонализация'),
	'users' => array('Права на играчите', 1, 'Персонализация'),
	'anticheat' => array('Анти-чиит система', 1, 'Персонализация'),
	'hlxbans' => array('HLX Bans', 1, 'Персонализация'),
	'ftp' => array('FTP достъп', 1, 'Персонализация'),
	'fps' => array('FPS рейтинг', 1, 'Персонализация'),
	'master' => array('Master функция', 1, 'Персонализация'),
	'motd' => array('Добавяне/Редактиране на MOTD', 1, 'Персонализация'),
	'scrollmsg' => array('Scroll съобщение', 1, 'Персонализация'),
	'redirects' => array('Пренасочвания', 1, 'Персонализация'),
	'maplist' => array('Списък с карти', 1, 'Персонализация'),
	'metamod' => array('Metamod', 1, 'Персонализация'),
	'cfgs' => array('Изпълняване на конфигурация', 1, 'Персонализация'),
	'cvars' => array('Персонални настройки (CVARS)', 1, 'Персонализация'),
	'kickban' => array('Изритай/Забрани играч', 1, 'Персонализация'),
	'hostname' => array('Име на сървър', 1, 'Настройки'),
	'rcon' => array('Сървър парола', 1, 'Настройки'),
	'passwdcp' => array('Административна парола', 1, 'Настройки'),
	'svpasswd' => array('Промяна на сървър парола', 1, 'Настройки'),
	'gametracker' => array('Динамичен банер', 1, 'Екстри'),
	'lastip' => array('Последно влизал от...', 1, 'Основни'),
	'log' => array('Сървър лог', 0),
	'fm' => array('Файлов мениджър', 1, 'Персонализация'),
	'radio' => array('Онлайн радио', 1, 'Персонализация'),
	'gamemenu' => array('GameMenu система', 1, 'Персонализация'),
	'changelog' => array('ChangeLog (списък с промени)', 0),
);

$players_limit = array(
	2 => 12,
	3 => 18,
	4 => 24,
	5 => 28,
);

$fps_labels = array(
	'100' => array(0),
	'500' => array(6.00),
	'1000' => array(10.00),
);

$zp_modes = array(9, 23, 25, 27);

$plugins_order = array(
	array('RegisterSystem', 'amxbans_core', 'amxbans_main', 'advanced_bans', 'amxwho'),
	array('admin', 'admincmd', 'adminhelp', 'adminslots', 'multilingual', 'menufront', 'cmdmenu', 'plmenu', 'mapsmenu', 'pluginmenu', 'adminchat', 'antiflood', 'scrollmsg', 'adminvote', 'nextmap', 'mapchooser', 'timeleft', 'pausecfg', 'statscfg', 'statsx', 'reaimdetector'),
	array('amx_gag', 'player_gag', 'amx_gag_retry', 'amx_gagv149', 'GagSystem', 'bbcommandsmenu', 'NoMoreShit'),
); 

?>
