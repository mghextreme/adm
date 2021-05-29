<?php
	include('../functions.php');
	connectDatabase();
	
	$id = $_POST['category'];
	
	$sql = "DELETE FROM `categories` WHERE `id`=" . $id;
	$query = mysql_query($sql);
	
	$sql2 = "DELETE FROM `subcategories` WHERE `categoryid`=" . $id;
	$query = mysql_query($sql2);
	
	die("ok");
?>