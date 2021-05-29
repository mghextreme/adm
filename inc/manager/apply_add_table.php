<?php
	session_start();
	include('../functions.php');
	include('../configuration.php');
	connectDatabase();
	
	$name = $_POST['tablename'];
	$link = strtolower($_POST['tablesql']);
	$admin = isset($_POST['tableadmin']) ? 1 : 0;
	$registration = isset($_POST['singletable']) ? 'singleregistration' : 'registration';
	
	$sql = "SELECT * FROM `{$link}`";
	$query = mysql_query($sql);
	
	if (!$query)
	{
		$sql = "SELECT `order` FROM `{$menu_db}` WHERE `order`<96 ORDER BY `order` DESC LIMIT 1";
		$rows = mysql_fetch_array(mysql_query($sql));
		$order = $rows['order'] + 1;
		
		$sql = "INSERT INTO `{$menu_db}`(`order`, `name`, `type`, `link`, `database`, `isadmin`) VALUES ({$order}, '{$name}', '{$registration}', '{$link}', '{$link}', {$admin})";
		mysql_query($sql);
		
		$sql2 = "CREATE TABLE `{$link}`(";
		$not = $registration != 'singleregistration' ? 'NOT' : 'DEFAULT';
		if ($registration != 'singleregistration')
		{
			$sql2 .= "
			`id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
			`order` int(11) NOT NULL,
			`name` varchar(300) DEFAULT NULL,";
		};
		$sql2 .= "
			`creationdate` datetime DEFAULT NULL,
			`modificationdate` datetime DEFAULT NULL,
			`creationuser` int(11) $not NULL,
			`modificationuser` int(11) $not NULL
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
		mysql_query($sql2);
		
		$sql3 = "CREATE TABLE `{$link}fields`(
			`id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
			`name` varchar(200) NOT NULL,
			`type` varchar(100) NOT NULL,
			`order` int(11) NOT NULL,
			`columnname` varchar(100) NOT NULL,
			`value` varchar(200) DEFAULT NULL,
			`required` tinyint(1) NOT NULL DEFAULT '0',
			`requiredtype` varchar(50) DEFAULT NULL
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
		mysql_query($sql3);
		
		if ($registration != 'singleregistration')
		{
			$sql = "INSERT INTO `" . $link . "fields`(`name`, `type`, `order`, `columnname`, `value`, `required`, `requiredtype`) VALUES
				('Nome','string',0,'name',NULL,1,' letternumber max[300] ')";
		}
		else $sql = "INSERT INTO `" . $link . "` VALUES ()";
		mysql_query($sql);
		
		die('ok');
	}
	else die('exist');
?>