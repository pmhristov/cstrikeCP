<?php
session_start();
require_once('../../includes/core.php');
require_once(INCLUDE_DIR . 'server_query.php');

if (!empty($rcon->getPlayers())) {
?>
<table class="table table-striped table-condensed table-bordered">
	<thead>
		<tr>
			<th>Играч</th>
			<th>Фрагове</th>
			<th>Свързан от</th>
		</tr>
	</thead>
<tbody>
<?php
foreach ($rcon->getPlayers() as $player) { ?>


	<tr>
		<td><?php echo $player['Name']; ?></td>
		<td><?php echo $player['Score']; ?></td>
		<td><?php echo gmdate('i', $player['Duration']); ?> минути</td>
	</tr>	
<?php } ?>
										
</tbody>

</table>
<?php } else {?>
<div style="text-align: center;">Няма активни играчи към този момент</div>
<?php } ?>