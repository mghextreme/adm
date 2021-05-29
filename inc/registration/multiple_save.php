<?php
	include('../functions.php');
	connectDatabase();
	
	$db = $_POST['db'];
	$order = $_POST['order'];
	$itemid = $_POST['itemid'];
	
	$sql = "SELECT `type` FROM `menu` WHERE `link`='{$db}'";
	$rows = mysql_fetch_assoc(mysql_query($sql));
	$setId = $rows['type'] == 'singleregistration' ? FALSE : TRUE;
	$idspec = $setId ? " WHERE `id`={$itemid}" : '';
	
	$sql = "SELECT `id`, `value` FROM `{$db}fields` WHERE `order`={$order}";
	$rows = mysql_fetch_assoc(mysql_query($sql));
	$newtable = $db . $rows['id'];
	
	$sql = "DELETE FROM `{$newtable}`{$idspec}";
	mysql_query($sql);
	
	if (isset($_POST['value']))
	{
		if ($rows['value'] == 'date')
		{
			for($i = 0; $i < count($_POST['value']); $i++)
			{
				$year = intval($_POST['value3'][$i]);
				$month = intval($_POST['value2'][$i]);
				$day = intval($_POST['value'][$i]);
				if ($day > 28)
				{
					switch($month)
					{
						case 02:
							if ($year % 4 == 0)
							{
								if ($day > 29)
									$day = 29;
							}
							else if ($day > 28)
								$day = 28;
							break;
						case 01:
						case 03:
						case 05:
						case 07:
						case 08:
						case 10:
						case 12:
							if ($day > 31)
								$day = 31;
							break;
						case 04:
						case 06:
						case 09:
						case 11:
							if ($day > 30)
								$day = 30;
							break;
					}
				}
				$value = $year . '-' . $month . '-' . $day;
				if ($setId)
				{ $sql = "INSERT INTO `{$newtable}`(`id`, `value`) VALUES ({$itemid}, '{$value}');\n"; }
				else $sql = "INSERT INTO `{$newtable}`(`value`) VALUES ('{$value}');\n";
				mysql_query($sql);
			}
		}
		else if ($rows['value'] == 'time')
		{
			for($i = 0; $i < count($_POST['value']); $i++)
			{
				$value = strval($_POST['value'][$i]) . ':' . strval($_POST['value2'][$i]) . ':' .strval($_POST['value3'][$i]);
				if ($setId)
				{ $sql = "INSERT INTO `{$newtable}`(`id`, `value`) VALUES ({$itemid}, '{$value}');\n"; }
				else $sql = "INSERT INTO `{$newtable}`(`value`) VALUES ('{$value}');\n";
				mysql_query($sql);
			}
		}
		else if ($rows['value'] == 'datetime')
		{
			for($i = 0; $i < count($_POST['value']); $i++)
			{
				$year = intval($_POST['value3'][$i]);
				$month = intval($_POST['value2'][$i]);
				$day = intval($_POST['value'][$i]);
				if ($day > 28)
				{
					switch($month)
					{
						case 02:
							if ($year % 4 == 0)
							{
								if ($day > 29)
									$day = 29;
							}
							else if ($day > 28)
								$day = 28;
							break;
						case 01:
						case 03:
						case 05:
						case 07:
						case 08:
						case 10:
						case 12:
							if ($day > 31)
								$day = 31;
							break;
						case 04:
						case 06:
						case 09:
						case 11:
							if ($day > 30)
								$day = 30;
							break;
					}
				}
				$value = $year . '-' . $month . '-' . $day . ' ' . intval($_POST['value4'][$i]) . ':' . intval($_POST['value5'][$i]) . ':' . intval($_POST['value6'][$i]);
				if ($setId)
				{ $sql = "INSERT INTO `{$newtable}`(`id`, `value`) VALUES ({$itemid}, '{$value}');\n"; }
				else $sql = "INSERT INTO `{$newtable}`(`value`) VALUES ('{$value}');\n";
				mysql_query($sql);
			}
		}
		else if ($rows['value'] == 'youtube')
		{
			for($i = 0; $i < count($_POST['value']); $i++)
			{
				$newValue = $_POST['value'][$i];
				if (strlen($_POST['value'][$i]) > 10)
				{
					$start = strpos($_POST['value'][$i], 'v=');
					$end = strpos($_POST['value'][$i], '&');
					
					if ($start < 0)
					{ $start = 0; }
					else $start += 2;
					
					if ($end > 0)
					{ $newValue = substr($_POST['value'][$i], $start, $end - $start); }
					else $newValue = substr($_POST['value'][$i], $start);
				}
				if ($setId)
				{ $sql = "INSERT INTO `{$newtable}`(`id`, `value`) VALUES ({$itemid}, '{$newValue}');\n"; }
				else $sql = "INSERT INTO `{$newtable}`(`value`) VALUES ('{$newValue}');\n";
				mysql_query($sql);
			}
		}
		else
		{
			foreach($_POST['value'] as $item)
			{
				if ($setId)
				{ $sql = "INSERT INTO `{$newtable}`(`id`, `value`) VALUES ({$itemid}, '{$item}');\n"; }
				else $sql = "INSERT INTO `{$newtable}`(`value`) VALUES ('{$item}');\n";
				mysql_query($sql);
			}
		}
	}
	
	die('ok');
?>