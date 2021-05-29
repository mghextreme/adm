<?php
	include('../functions.php');
	include('../configuration.php');
	connectDatabase();
	
	$db = $_POST['db'];
	$order = countItems($db . 'fields');
	$requiredtype = $_POST['requiredtype'];
	$value = ($_POST['value'] == -1 ? 'NULL' : ($_POST['value'] == 'table' ? "'" . $_POST['value'] . '-' . $_POST['value2'] . "'" : "'" . $_POST['value'] . "'"));
	
	if ($requiredtype != 0)
	{
		if (substr($requiredtype, 0, 1) != ' ')
			$requiredtype = ' ' . $requiredtype . ' ';
		
		$requiredtype = "'" . $requiredtype . "'";
		
		if (strpos($_POST['requiredtype'], 'max[') !== FALSE)
		{ $max = substr($_POST['requiredtype'], strpos($_POST['requiredtype'], 'max[') + 4, strpos($_POST['requiredtype'], ']', strpos($_POST['requiredtype'], 'max[') + 4) - strpos($_POST['requiredtype'], 'max[') - 4); }
	}
	else if ($_POST['type'] == 'string' || $_POST['type'] == 'text' || $_POST['type'] == 'url' || ($_POST['type'] == 'multiple' && ($_POST['value'] == 'string' || $_POST['value'] == 'text' || $_POST['value'] == 'url')))
	{
		$max = $type == 'text' ? '2000' : '300';
		$requiredtype = "' max[" . $max . "] '";
	}
	else $requiredtype = 'NULL';
	
	$sql = "INSERT INTO `{$db}fields`(`name`, `type`, `order`, `columnname`, `value`, `required`, `requiredtype`) VALUES ('" . $_POST['name'] . "','" . $_POST['type'] . "'," . $order . ",'" . $_POST['columnname'] . "'," . $value . "," . $_POST['required'] . "," . $requiredtype . ")";
	mysql_query($sql);
	
	if ($_POST['type'] != 'multiple')
	{
		switch($_POST['type'])
		{
			case 'url':
			case 'string':
			case 'multiple':
				$char = isset($max) ? strval($max) : '300';
				$type = "varchar($char)";
				break;
			case 'text':
				$char = isset($max) ? strval($max) : '2000';
				$type = "varchar($char)";
				break;
			case 'category':
				$type = 'varchar(7)';
				break;
			case 'youtube':
				$type = 'varchar(100)';
				break;
			case 'date':
				$type = 'date';
				break;
			case 'datetime':
				$type = 'datetime';
				break;
			case 'time':
				$type = 'time';
				break;
			case 'table':
			case 'options':
			case 'singleimage':
			case 'image':
			case 'files':
				$type = 'int(11)';
				break;
		}
		
		$sql = "ALTER TABLE `" . $db . "` ADD `" . $_POST['columnname'] . "` " . $type;
		mysql_query($sql);
	}
	
	$sql = "SELECT `id` FROM `{$db}fields` ORDER BY `id` DESC LIMIT 1";
	$rows = mysql_fetch_assoc(mysql_query($sql));
	
	if ($_POST['type'] == 'multiple')
	{
		switch($_POST['value'])
		{
			case 'url':
			case 'string':
				$mulchar = isset($max) ? strval($max) : '300';
				$multype = "varchar($mulchar)";
				break;
			case 'text':
				$mulchar = isset($max) ? strval($max) : '2000';
				$multype = "varchar($mulchar)";
				break;
			case 'category':
				$multype = 'varchar(7)';
				break;
			case 'youtube':
				$multype = 'varchar(100)';
				break;
			case 'date':
				$multype = 'date';
				break;
			case 'datetime':
				$multype = 'datetime';
				break;
			case 'time':
				$multype = 'time';
				break;
			case 'table':
				$multype = 'int(11)';
				break;
		}
		
		$sql2 = "SELECT `type` FROM `{$menu_db}` WHERE `database`='{$db}'";
		$row2 = mysql_fetch_assoc(mysql_query($sql2));
		
		$tablename = $db . $rows['id'];
		$sql2 = "CREATE TABLE `{$tablename}`(";
		if ($row2['type'] != 'singleregistration')
		{
			$sql2 .= "
			`id` int(11) NOT NULL,";
		};
		$sql2 .= "
			`order` int(11) DEFAULT NULL,
			`value` {$multype} DEFAULT NULL,
			`fullid` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
		mysql_query($sql2);
	}
	
	die($rows['id']);
?>