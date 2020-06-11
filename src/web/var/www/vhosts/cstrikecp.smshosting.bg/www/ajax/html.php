<?php
require '../../includes/core.php';

if (!empty($_GET['type']) && !empty($_GET['content'])) {
	switch($_GET['type']) {
		case 'plugin':
			$plugin_info = $DB->GetRow('SELECT * FROM `pluginlist` WHERE `name` = ?', $_GET['content']);
			if (empty($plugin_info['description'])) {
				$description = '<a href="' . $plugin_info['url'] . '" target="_blank">' . $plugin_info['url'] . '</a>';
			}
			else {
				$description = $plugin_info['description'];
			}
			$plugin_list = $DB->GetAll('SELECT * FROM `pluginlist_content` WHERE `plugin` = ?', $_GET['content']);
			
			$server_info = $DB->GetRow('SELECT * FROM `servers` WHERE `id` = ?', array($_SESSION['id']));
			echo '<strong>Съдържа плъгините:</strong><br />';
			foreach ($plugin_list as $plugin_content) {
				echo $plugin_content['content'] . '.amxx<br />';
			}
			echo '<br /><b>Описание на плъгина:</b><br /> ' . $description . '<br />'; 
			
		break;	
		
		case 'mode':
		$server_info = $DB->GetRow('SELECT * FROM `servers` WHERE `id` = ?', $_SESSION['id']);
?>
		<img src="/img/icons/essen/16/bookmark.png" /> <strong>Описание на мода:</strong>
		<?php echo $mode_description[$_GET['content']]; ?>
		<?php
		if (!empty($mode_plugins[$_GET['content']])) { ?>
		<br /><br />
		<img src="/img/icons/essen/16/order.png" /> <strong>Допълнителни плъгини към мод-а:</strong>
		<br />
		<?php 
			foreach($mode_plugins[$_GET['content']] as $mode_plugin) {
				echo $mode_plugin . '<br />';
			}
			
		}
		?>
		<br />
		
	
<?php
		break;
		case 'anticheat':
			echo '<b>Линк с описание на Античиит системата:</b><br /> <a href="' . $anticheat_labels[$_GET['content']][2] . '" target="_blank"> ' . $anticheat_labels[$_GET['content']][2] . '</a>';
		break;		
	}
}
?>
