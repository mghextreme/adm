<?php
	include('../functions.php');
	connectDatabase();
	$list = $_POST['list'];
	
	for ($i = 0; $i < count($list); $i++)
	{
		$sql = "UPDATE `{$_POST['link']}fields` SET `order`=" . $i . " WHERE `columnname`='" . $list[$i] . "'";
		$query = mysql_query($sql);
	}
	
	die('ok');
	//die('count = ' . $count);
?>