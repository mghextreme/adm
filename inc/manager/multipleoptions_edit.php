<?php
	include('../functions.php');
	connectDatabase();
	
	$db = $_POST['db'];
	$id = $_POST['id'];
	$number = $_POST['number'];
	
	//Get and Sets `value`
	$sql = "SELECT `value` FROM `{$db}fields` WHERE `id`=" . $id;
	$rows = mysql_fetch_assoc(mysql_query($sql));
	$value = $rows['value'];
	
	//Add item
	$sql = "UPDATE `multipleoptions` SET `value`='{$_POST['value']}' WHERE `id`={$value} && `number`={$number}";
	mysql_query($sql);
	
	die('ok');
?>