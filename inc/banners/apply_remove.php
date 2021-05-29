<?php
	include('../configuration.php');
	include('../functions.php');
	connectDatabase();
	
	$id = $_POST['id'];
	
	$sql = "SELECT `link` FROM `banners` WHERE `id`=" . $id;
	$rows = mysql_fetch_assoc(mysql_query($sql));
	$file = $absolute_path . $rows['link'];
	
	if (file_exists($file))
		unlink($file);
	
	$sql = "DELETE FROM `banners` WHERE `id`=" . $id;
	$query = mysql_query($sql);
	
	die("ok");
?>