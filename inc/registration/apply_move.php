<?php
	include('../functions.php');
	connectDatabase();
	
	$db = getDatabase($_POST['menu']);
	$list = $_POST['list'];
	
	for ($i = 0; $i < count($list); $i++)
	{
		$sql = "UPDATE $db SET `order`=" . $i . " WHERE `id`=" . $list[$i];
		$query = mysql_query($sql);
	}
	
	die('ok');
	//die('count = ' . $count);
?>