<?php
	include('../functions.php');
	connectDatabase();
	
	$db = $_POST['db'];
	$order = $_POST['order'];
	$fullid = $_POST['id'];
	
	$sql = "SELECT `id` FROM `{$db}fields` WHERE `order`={$order}";
	$rows = mysql_fetch_assoc(mysql_query($sql));
	
	$sql = "DELETE FROM `{$db}{$rows['id']}` WHERE `fullid`={$fullid}";
	mysql_query($sql);
	
	die('ok');
?>