<?php
	include('../functions.php');
	connectDatabase();
	
	$link = $_POST['link'];
	$name = $_POST['name'];
	$id = $_POST['albumID'];
	
	$sql = "UPDATE `albuns` SET `name`='" . $name . "' WHERE `link`='" . $link . "' && `id`=" . $id ;
	mysql_query($sql);
	
	die('ok');
?>