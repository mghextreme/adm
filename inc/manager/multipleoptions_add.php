<?php
	include('../functions.php');
	connectDatabase();
	
	$db = $_POST['db'];
	$id = $_POST['id'];
	
	//Get and Sets `value`
	$sql = "SELECT `value` FROM `{$db}fields` WHERE `id`=" . $id;
	$query = mysql_query($sql);
	if ($query != FALSE && mysql_num_rows($query) > 0)
	{
		$rows = mysql_fetch_assoc($query);
		$value = $rows['value'];
	}
	else $value = NULL;
	$count = 0;
	if ($value == NULL || $value < 1)
	{
		$sql = "SELECT `id` FROM `multipleoptions` ORDER BY `id` DESC LIMIT 1";
		$rows = mysql_fetch_assoc(mysql_query($sql));
		$value = ceil($rows['id']) + 1;
		
		$sql = "UPDATE `{$db}fields` SET `value`={$value} WHERE `id`=" . $id;
		mysql_query($sql);
	}
	else
	{
		//Count Existing Items
		$sql = "SELECT `number` FROM `multipleoptions` WHERE `id`={$value} ORDER BY `number` DESC LIMIT 1";
		$rows = mysql_fetch_assoc(mysql_query($sql));
		$count = ceil($rows['number']) + 1;
	}
	
	//Add item
	$sql = "INSERT INTO `multipleoptions`(`id`, `value`, `number`) VALUES ({$value}, '{$_POST['value']}', {$count})";
	mysql_query($sql);
	
	die('ok');
?>