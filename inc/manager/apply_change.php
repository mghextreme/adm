<?php
	include('../functions.php');
	connectDatabase();
	
	$db = $_POST['db'];
	$id = $_POST['id'];
	
	$sql = "SELECT * FROM `" . $db . "fields` WHERE `id`=" . $id;
	$rows = mysql_fetch_assoc(mysql_query($sql));
	
	if ($rows['name'] != $_POST['name'])
	{ $name = $_POST['name']; }
	
	if ($rows['columnname'] != $_POST['columnname'])
	{ $columnname = $_POST['columnname']; }
	
	if ($rows['type'] != $_POST['type'])
	{
		$extype = $rows['type'];
		$type = $_POST['type'];
	}
	
	if ($rows['required'] != $_POST['required'])
	{ $required = $_POST['required']; }
	
	if ($rows['requiredtype'] != $_POST['requiredtype'])
	{
		$requiredtype = $_POST['requiredtype'];
		if ($requiredtype != -1)
		{
			if (substr($requiredtype, 0, 1) != ' ')
				$requiredtype = ' ' . $requiredtype . ' ';
			
			$requiredtype = "'" . $requiredtype . "'";
		}
		else $requiredtype = 'NULL';
		
		if (strpos($_POST['requiredtype'], 'max[') !== FALSE)
		{
			$max = substr($_POST['requiredtype'], strpos($_POST['requiredtype'], 'max[') + 4, strpos($_POST['requiredtype'], ']', strpos($_POST['requiredtype'], 'max[') + 4) - strpos($_POST['requiredtype'], 'max[') - 4);
			if (!isset($type) || !isset($columnname))
			{
				$extype = $rows['type'];
				$columnname = $_POST['columnname'];
			}
		}
		else if (strpos($rows['requiredtype'], 'max[') !== FALSE)
		{
			if (!isset($type) || !isset($columnname))
			{
				$extype = $rows['type'];
				$columnname = $_POST['columnname'];
			}
			
			if ($_POST['type'] == 'string' || $_POST['type'] == 'text' || $_POST['type'] == 'url')
			{
				$max = $_POST['type'] == 'text' ? '2000' : '300';
				$requiredtype = "' max[" . $max . "] '";
			}
		}
	}
	else if ($_POST['type'] == 'string' || $_POST['type'] == 'text' || $_POST['type'] == 'url')
	{
		if (strpos($rows['requiredtype'], 'max[') === FALSE)
		{
			$max = $_POST['type'] == 'text' ? '2000' : '300';
			$requiredtype = "'" . $rows['requiredtype'] . ' max[' . $max . '] ' . "'";
		}
	}
	
	if ($rows['value'] != $_POST['value'])
	{
		if ($_POST['value'] != -1)
			$value = $_POST['value'];
		
		if ($_POST['type'] == 'multiple' && $_POST['value'] == 'table')
		{ $value = $_POST['value'] . '-' . $_POST['value2']; }
	}
	
	if ($_POST['type'] == 'multiple')
	{
		if ($_POST['value'] != $rows['value'])
		{
			if ($_POST['value'] == 'url' || $_POST['value'] == 'text' || $_POST['value'] == 'string')
			{
				if (strpos($rows['requiredtype'], 'max[') === FALSE)
				{
					$max = $_POST['type'] == 'text' ? '2000' : '300';
					$requiredtype = "'" . $rows['requiredtype'] . ' max[' . $max . '] ' . "'";
				}
			}
			
			switch($_POST['value'])
			{
				case 'url':
				case 'string':
				case 'text':
					$muldefinition = 'VARCHAR(' . strval($max) . ') CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL';
					break;
				case 'youtube':
					$muldefinition = 'VARCHAR(100) NULL DEFAULT NULL';
					break;
				case 'date':
					$muldefinition = 'DATE NULL DEFAULT NULL';
					break;
				case 'datetime':
					$muldefinition = 'DATETIME NULL DEFAULT NULL';
					break;
				case 'time':
					$muldefinition = 'TIME NULL DEFAULT NULL';
					break;
				case 'table':
					$muldefinition = 'INT(11) NULL DEFAULT NULL';
					break;
			}
	
			$sql2 = "DELETE FROM `{$db}{$id}`";
			mysql_query($sql2);
	
			$sql2 = "ALTER TABLE `{$db}{$id}` CHANGE `value` `value` {$muldefinition}";
			mysql_query($sql2);
		}
	}
	
	$sql = "UPDATE `" . $db . "fields` SET ";
	
	$fields = 0;
	if (isset($name))
	{
		$sql .= "`name`='" . $name . "'";
		$fields++;
	}
	
	if (isset($columnname))
	{
		if ($fields > 0)
			$sql .= ", ";
		$sql .= "`columnname`='" . $columnname . "'";
		$fields++;
	}
	
	if (isset($type))
	{
		if ($fields > 0)
			$sql .= ", ";
		$sql .= "`type`='" . $type . "'";
		$fields++;
	}
	
	if (isset($value))
	{
		if ($fields > 0)
			$sql .= ", ";
		$sql .= "`value`='" . $value . "'";
		$fields++;
	}
	
	if (isset($required))
	{
		if ($fields > 0)
			$sql .= ", ";
		$sql .= "`required`=" . $required;
		$fields++;
	}
	
	if (isset($requiredtype))
	{
		if ($fields > 0)
			$sql .= ", ";
		$sql .= "`requiredtype`=" . $requiredtype;
		$fields++;
	}
	
	$sql .= " WHERE `id`=" . $id;
	if ($fields > 0)
		mysql_query($sql);
	
	if (isset($columnname))
	{
		if ($_POST['type'] != $rows['type'] || ($_POST['type'] == 'url' || $_POST['type'] == 'string' || $_POST['type'] == 'text'))
		{
			switch($_POST['type'])
			{
				case 'url':
				case 'string':
					$char = isset($max) ? strval($max) : '300';
					$definition = ' VARCHAR(' . $char . ') CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL';
					break;
				case 'text':
					$char = isset($max) ? strval($max) : '2000';
					$definition = ' VARCHAR(' . $char . ') CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL';
					break;
				case 'category':
					$definition = ' VARCHAR(7) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL';
					break;
				case 'youtube':
					$definition = ' VARCHAR(100) NULL DEFAULT NULL';
					break;
				case 'date':
					$definition = ' DATE NULL DEFAULT NULL';
					break;
				case 'datetime':
					$definition = ' DATETIME NULL DEFAULT NULL';
					break;
				case 'time':
					$definition = ' TIME NULL DEFAULT NULL';
					break;
				case 'options':
				case 'image':
				case 'files':
				case 'table':
				case 'singleimage':
					$definition = ' INT(11) NULL DEFAULT NULL';
					break;
				default:
					$definition = ' INT(11) NULL DEFAULT NULL';
					break;
			}
		}
		else $definition = '';
		
		$sql2 = "ALTER TABLE `" . $db . "` CHANGE `" . $rows['columnname'] . "` `" . $columnname . "`" . $definition;
		mysql_query($sql2);
	}
	
	if (isset($type))
	{
		if (!isset($columnname))
			$columnname = $rows['columnname'];
		
		$sql3 = "UPDATE `" . $db . "` SET `" . $columnname . "`=NULL";
		mysql_query($sql3);
	}
	
	die('ok');
?>