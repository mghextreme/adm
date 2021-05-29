<?php
	include('../functions.php');
	connectDatabase();
	
	$db = $_POST['db'];
	
	$sql = "SELECT * FROM `" . $db . "` WHERE `type`!='home' && `type`!='logout' && `type`!='manager' && `link`!='users'";
	$query = mysql_query($sql);
	
	if (isset($_POST['admin']))
		$admin = $_POST['admin'];
	else $admin = NULL;
	
	$sql = "UPDATE `" . $db . "` SET `isadmin`=1 WHERE ";
	$sql2 = "UPDATE `" . $db . "` SET `isadmin`=0 WHERE ";
	for($i = 0; $rows = mysql_fetch_assoc($query); $i)
	{
		if ($i > 0)
			$sql .= ' || ';
		else $i = 1;
		
		$value1 = "`link`='" . $rows['link'] . "'";
		$sql .= $value1;
		
		if ($admin != NULL)
		{
			if (in_array($rows['link'], $admin))
			{
				if ($i > 1)
				{ $sql2 .= ' || '; }
				else $i = 2;
				$sql2 .= $value1;
			}
		}
	}
	
	mysql_query($sql);
	mysql_query($sql2);
	
	die('ok');
?>