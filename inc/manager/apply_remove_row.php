<?php
	include('../functions.php');
	include('../configuration.php');
	connectDatabase();
	$db = $_POST['db'];
	$order = $_POST['order'];
	
	$sql = "SELECT `columnname`, `type`, `value`, `id` FROM `{$db}fields` WHERE `order`=" . $order;
	$rows = mysql_fetch_assoc(mysql_query($sql));
	
	if ($rows['type'] == 'multiple')
	{
		$sql = "DROP TABLE `{$db}{$rows['id']}`";
		mysql_query($sql);
	}
	else if ($rows['type'] == 'image' || $rows['type'] == 'files')
	{
		$sql2 = "SELECT `{$rows['columnname']}` FROM `{$db}` WHERE `{$rows['columnname']}` IS NOT NULL";
		$query2 = mysql_query($sql2);
		$albums = NULL;
		while ($rows2 = mysql_fetch_assoc($query2))
		{ $albums[] = $rows2[$rows['columnname']]; }
		if ($albums != NULL)
		{
			foreach ($albums as $abm)
			{
				$sql2 = "SELECT `link` FROM `albuns` WHERE `id`=" . $abm;
				$query2 = mysql_query($sql2);
				while ($rows2 = mysql_fetch_assoc($query2))
				{
					if (file_exists($absolute_path . "/" . $rows2['link']))
						unlink($absolute_path . "/" . $rows2['link']);
				}
				mysql_query("SELECT `link` FROM `albuns` WHERE `id`={$abm}");
			}
		}
	}
	else if ($rows['type'] == 'options')
	{
		$sql = "DELETE FROM `multipleoptions` WHERE `id`=" . $rows['value'];
		mysql_query($sql);
	}
	
	if ($rows['type'] != 'multiple')
	{
		$sql = "ALTER TABLE `{$db}` DROP `" . $rows['columnname'] . "`";
		mysql_query($sql);
	}
	
	$sql = "DELETE FROM `{$db}fields` WHERE `order`=" . $order;
	mysql_query($sql);
	
	//setOrder($db, $numb, $iStart, $iEnd)
	setOrder($db . 'fields', -1, $order, 0);
	
	die('ok');
?>