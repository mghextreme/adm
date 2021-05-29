<?php
	include('../functions.php');
	include('../configuration.php');
	connectDatabase();
	
	$db = $_POST['table'];
	
	$sql = "SELECT `id` FROM `{$db}fields` WHERE `type`='multiple'";
	$query = mysql_query($sql);
	while ($rows = mysql_fetch_assoc($query))
	{
		$sql = "DROP TABLE `{$db}{$rows['id']}`";
		mysql_query($sql);
	}
	
	$sql = "SELECT `value` FROM `{$db}fields` WHERE `type`='options'";
	$query = mysql_query($sql);
	while ($rows = mysql_fetch_assoc($query))
	{
		$sql = "DELETE FROM `multipleoptions` WHERE `id`=" . $rows['value'];
		mysql_query($sql);
	}
	
	$sql = "SELECT `columnname` FROM `{$db}fields` WHERE `type`='image' || `type`='files'";
	$query = mysql_query($sql);
	while ($rows = mysql_fetch_assoc($query))
	{
		$sql2 = "SELECT `{$rows['columnname']}` FROM `{$db}` WHERE `{$rows['columnname']}` IS NOT NULL";
		$query2 = mysql_query($sql2);
		while ($rows2 = mysql_fetch_assoc($query2))
		{
			$sql3 = "SELECT `link` FROM `albuns` WHERE `id`={$rows2[$rows['columnname']]}";
			$query3 = mysql_query($sql3);
			if ($query3 != NULL && $query3 != FALSE)
			{
				$links = NULL;
				while ($rows3 = mysql_fetch_assoc($query3))
				{ $links[] = $rows3['link']; }
				
				$sql4 = "DELETE FROM `albuns` WHERE `id`={$rows2[$rows['columnname']]}";
				mysql_query($sql4);
				
				if ($links != NULL)
				{
					foreach($links as $lk)
					{
						if (file_exists($absolute_path . "/" . $lk))
							unlink($absolute_path . "/" . $lk);
					}
				}
			}
		}
	}
	
	$sql = "DELETE FROM `{$menu_db}` WHERE `database`='{$db}'";
	mysql_query($sql);
	
	$sql = "DROP TABLE `{$db}`";
	mysql_query($sql);
	
	$sql = "DROP TABLE `{$db}fields`";
	mysql_query($sql);
	
	die('ok');
?>