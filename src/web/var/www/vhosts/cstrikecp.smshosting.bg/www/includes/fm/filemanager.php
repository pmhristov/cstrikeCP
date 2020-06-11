<?php

/**
 * +-------------------------------------------------------------------+
 * |                    F I L E M A N A G E R   (v10.27)               |
 * |                                                                   |
 * | Copyright Gerd Tentler                www.gerd-tentler.de/tools   |
 * | Created: Dec. 7, 2006                 Last modified: Feb. 2, 2014 |
 * +-------------------------------------------------------------------+
 * | This program may be used and hosted free of charge by anyone for  |
 * | personal purpose as long as this copyright notice remains intact. |
 * |                                                                   |
 * | Obtain permission before selling the code for this program or     |
 * | hosting this software on a commercial website or redistributing   |
 * | this software over the Internet or in any other medium. In all    |
 * | cases copyright must remain intact.                               |
 * +-------------------------------------------------------------------+
 */

include_once('class/FileManager.php');

?>
<!DOCTYPE html>
<html>
<head>
<title>File Manager</title>
</head>
<body class="fmBody">
<table border="0" style="width:100%; height:100%"><tr>
<td align="center">
<?php

$FileManager = new FileManager();
print $FileManager->create();

?>
</td>
</tr></table>
</body>
</html>