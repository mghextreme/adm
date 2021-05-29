<?php
	include('../functions.php');
	include('../configuration.php');
	connectDatabase();
	$db = getDatabase($_POST['page']);
	$id = $_POST['id'];
	
	$sql = "SELECT `columnname`, `id`, `type` FROM `{$db}fields` WHERE `type`='image' || `type`='multiple' || `type`='files'";
	$query = mysql_query($sql);
	$fields = NULL;
	while ($row = mysql_fetch_assoc($query))
	{
		switch($row['type'])
		{
			case 'image':
			case 'files':
				$sql = "SELECT `{$row['columnname']}` FROM `{$db}` WHERE `id`={$id}";
				$value = mysql_fetch_assoc(mysql_query($sql));
				
				$sql2 = "SELECT `link` FROM `albuns` WHERE `id`={$value[$row['columnname']]}";
				$query2 = mysql_query($sql2);
				if ($query2 != NULL && $query2 != FALSE)
				{
					$links = NULL;
					while ($row2 = mysql_fetch_assoc($query2))
					{ $links[] = $row2['link']; }
					
					$sql = "DELETE FROM `albuns` WHERE `id`={$value[$row['columnname']]}";
					mysql_query($sql);
					
					if ($links != NULL)
					{
						foreach($links as $lk)
						{
							if (file_exists($absolute_path . "/" . $lk))
								unlink($absolute_path . "/" . $lk);
						}
					}
				}
				break;
			case 'multiple':
				$sql = "DELETE FROM `{$db}{$row['id']}` WHERE `id`={$id}";
				mysql_query($sql);
				break;
		}
	}
	
	$sql = "SELECT `order` FROM `{$db}` WHERE `id`={$id}";
	$query = mysql_query($sql);
	while($rows = mysql_fetch_array($query))
		$order = $rows['order'];
	
	$sql = "DELETE FROM `{$db}` WHERE `id`={$id}";
	mysql_query($sql);
	
	//setOrder($db, $numb, $iStart, $iEnd)
	setOrder($db, -1, $order, 0);
	die("ok");
?>