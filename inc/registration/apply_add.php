<?php
	session_start();
	include('../functions.php');
	include('../configuration.php');
	connectDatabase();
	$menu = $_POST['menu'];
	$db = getDatabase($menu);
	$fields = getFieldsTypes($db);
	$time = GetTime();
	
	//Set new 'order' to every item
	//setOrder($db, $numb, $iStart, $iEnd)
	setOrder($db, +1, 0, 0);
	
	$sql = "SELECT `id` FROM `{$users_db}` WHERE `login`='{$_SESSION['usere2']}'";
	$itemid = mysql_fetch_assoc(mysql_query($sql));
	
	$sql = "INSERT INTO `{$db}`(`order`, `creationdate`, `modificationdate`, `creationuser`, `modificationuser`) VALUES (0, '{$time['sql']}', '{$time['sql']}', '{$itemid['id']}', '{$itemid['id']}')";
	mysql_query($sql);
	
	$sql = "SELECT `id` FROM `" . $db . "` WHERE `order`=0";
	$rows = mysql_fetch_array(mysql_query($sql));
	$id = $rows['id'];
	
	$multipleTable = NULL;
	
	$sql = 'UPDATE `' . $db . "` SET ";
	
	for ($i = 0; $i < count($fields); $i++)
	{
		switch($fields[$i]['type'])
		{
			case 'multiple':
				$multipleTable[] = $db . $fields[$i]['id'];
				break;
			case 'date':
				$sql .= "`" . $fields[$i]['columnname'] . "`=";
				if (isset($_POST[$fields[$i]['columnname'] . '_year']))
				{
					if (strlen($_POST[$fields[$i]['columnname'] . '_year']) > 3 && $_POST[$fields[$i]['columnname'] . '_month'] != 'NULL' && strlen($_POST[$fields[$i]['columnname'] . '_day']) > 0)
					{
						$day = $_POST[$fields[$i]['columnname'] . '_day'];
						switch($_POST[$fields[$i]['columnname'] . '_month'])
						{
							case '4':
							case '6':
							case '9':
							case '11':
								$day = $day > 30 ? $day = 30 : $day;
								break;
							case '2':
								if (intval($_POST[$fields[$i]['columnname'] . '_year']) % 4 == 0)
									$day = $day > 29 ? $day = 29 : $day;
								else $day = $day > 28 ? $day = 28 : $day;
								break;
						}
						$sql .= "'" . $_POST[$fields[$i]['columnname'] . '_year'] . "-" . $_POST[$fields[$i]['columnname'] . '_month'] . "-{$day}'";
					}
					else $sql .= 'NULL';
				}
				else $sql .= 'NULL';
				$sql .= ', ';
				break;
			case 'time':
				$sql .= "`" . $fields[$i]['columnname'] . "`=";
				if (isset($_POST[$fields[$i]['columnname'] . '_hour']))
				{
					if (strlen($_POST[$fields[$i]['columnname'] . '_hour']) > 0 && strlen($_POST[$fields[$i]['columnname'] . '_min']) > 0 && strlen($_POST[$fields[$i]['columnname'] . '_sec']) > 0)
						$sql .= "'" . $_POST[$fields[$i]['columnname'] . '_hour'] . ":" . $_POST[$fields[$i]['columnname'] . '_min'] . ":" . $_POST[$fields[$i]['columnname'] . '_sec'] . "'";
					else $sql .= 'NULL';
				}
				else $sql .= 'NULL';
				$sql .= ', ';
				break;
			case 'datetime':
				$sql .= "`" . $fields[$i]['columnname'] . "`=";
				if (isset($_POST[$fields[$i]['columnname'] . '_year']) && isset($_POST[$fields[$i]['columnname'] . '_hour']))
				{
					if (strlen($_POST[$fields[$i]['columnname'] . '_year']) > 3 && $_POST[$fields[$i]['columnname'] . '_month'] != 'NULL' && strlen($_POST[$fields[$i]['columnname'] . '_day']) > 0 && strlen($_POST[$fields[$i]['columnname'] . '_hour']) > 0 && strlen($_POST[$fields[$i]['columnname'] . '_min']) > 0 && strlen($_POST[$fields[$i]['columnname'] . '_sec']) > 0)
					{
						$day = $_POST[$fields[$i]['columnname'] . '_day'];
						switch($_POST[$fields[$i]['columnname'] . '_month'])
						{
							case '4':
							case '6':
							case '9':
							case '11':
								$day = $day > 30 ? $day = 30 : $day;
								break;
							case '2':
								if (intval($_POST[$fields[$i]['columnname'] . '_year']) % 4 == 0)
									$day = $day > 29 ? $day = 29 : $day;
								else $day = $day > 28 ? $day = 28 : $day;
								break;
						}
						$sql .= "'" . $_POST[$fields[$i]['columnname'] . '_year'] . "-" . $_POST[$fields[$i]['columnname'] . '_month'] . "-{$day} " . $_POST[$fields[$i]['columnname'] . '_hour'] . ":" . $_POST[$fields[$i]['columnname'] . '_min'] . ":" . $_POST[$fields[$i]['columnname'] . '_sec'] . "', ";
					}
					else $sql .= 'NULL, ';
				}
				else $sql .= 'NULL, ';
				break;
			case 'image':
			case 'files':
				$sql .= "`" . $fields[$i]['columnname'] . "`=";
				
				$sql2 = "SELECT * FROM `albuns` WHERE `id`='{$_POST[$fields[$i]['columnname']]}'";
				$query2 = mysql_query($sql2);
				if ($query2 != FALSE && $query2 != NULL && mysql_num_rows($query2) != 0)
				{
					$newID = 0;
					$sql3 = "SELECT DISTINCT `id` FROM `albuns` ORDER BY `id` DESC LIMIT 1";
					$query3 = mysql_query($sql3);
					if ($query3 != FALSE && $query3 != NULL)
					{
						$row3 = mysql_fetch_assoc($query3);
						$newID = $row3['id'] + 1;
						
						if ($newID <= 0)
							$newID = 1;
					}
					else $newID = 1;
					
					mysql_query("UPDATE `albuns` SET `id`={$newID} WHERE `id`='{$_POST[$fields[$i]['columnname']]}'");
					$sql .= strval($newID);
				}
				else $sql .= 'NULL';
				$sql .= ', ';
				break;
			case 'string':
			case 'text':
			case 'table':
			case 'options':
				$sql .= "`" . $fields[$i]['columnname'] . "`=";
				$value = isset($_POST[$fields[$i]['columnname']]) ? $_POST[$fields[$i]['columnname']] : NULL;
				if ($value != NULL && $value != 'NULL')
					$sql .= "'" . $value . "', ";
				else $sql .= "NULL, ";
				break;
			case 'password':
				$sql .= "`" . $fields[$i]['columnname'] . "`=";
				$value = isset($_POST[$fields[$i]['columnname']]) ? $_POST[$fields[$i]['columnname']] : NULL;
				$sql .= "'" . crypt($value) . "', ";
				break;
			case 'category':
				$sql .= "`" . $fields[$i]['columnname'] . "`=";
				$cat = $fields[$i]['columnname'];
				if ($_POST[$cat] != 'z')
				{
					if (isset($_POST[$cat . '_sub']) && $_POST[$cat . '_sub'] != 'z')
						$value = "'" . $_POST[$cat] . '-' . $_POST[$cat . '_sub'] . "'";
					else $value = $_POST[$cat];
				}
				else $value = "NULL";
				$sql .= $value . ', ';
				break;
			case 'url':
				$sql .= "`" . $fields[$i]['columnname'] . "`=";
				$value = isset($_POST[$fields[$i]['columnname']]) ? $_POST[$fields[$i]['columnname']] : NULL;
				
				if (strlen($value) > 0 && $value != '#' && $value != NULL && $value != 'NULL')
				{
					if (substr($value, 0, 6) != 'ftp://' && substr($value, 0, 7) != 'http://' && substr($value, 0, 8) != 'https://')
						$value = 'http://' . $value;
					
					$sql .= "'" . $value . "', ";
				}
				else $sql .= "NULL, ";
				break;
			case 'youtube':
				$value = isset($_POST[$fields[$i]['columnname']]) ? $_POST[$fields[$i]['columnname']] : NULL;
				$sql .= "`" . $fields[$i]['columnname'] . "`='";
				$newValue = $value;
				if (strlen($value) > 10)
				{
					$start = strpos($value, 'v=');
					$end = strpos($value, '&');
					
					if ($start < 0)
					{ $start = 0; }
					else $start += 2;
					
					if ($end > 0)
					{
						$newValue = substr($value, $start, $end - $start);
					}
					else $newValue = substr($value, $start);
				}
				$sql .= $newValue . "', ";
				break;
		}
	}
	$sql .= " `order`=0 WHERE `id`=" . $id;
	mysql_query($sql);
	
	if ($multipleTable != NULL)
	{
		foreach($multipleTable as $item)
		{
			$sql = "UPDATE `{$item}` SET `id`={$id} WHERE `id`=-1";
			mysql_query($sql);
		}
	}
	
	die('ok');
?>