<?php
	include('../functions.php');
	connectDatabase();
	
	$id = $_POST['albumID'];
	$links = $_POST['link'];
	
	for ($i = 0; $i < count($links); $i++)
	{
		mysql_query("UPDATE `albuns` SET `order`='{$i}' WHERE `id`='" . $id . "' && `link`='" . $links[$i] . "'");
	}
	
	die('ok');
?>