<?php
	include('../functions.php');
	connectDatabase();
	$albumID = $_POST['albumID'];
	$link = $_POST['link'];
	
	$sql = "SELECT `cover` FROM `albuns` WHERE id=" . $albumID . " AND link='" . $link . "'";
	$rows = mysql_fetch_array(mysql_query($sql));
	
	$sql = "UPDATE `albuns` SET cover=0 WHERE id=" . $albumID;
	mysql_query($sql);
	
	if ($rows[0] == 0)
	{
		$sql = "UPDATE `albuns` SET cover=1 WHERE id=" . $albumID . " AND link='" . $link . "'";
		mysql_query($sql);
	}
	
	die('ok');
?>