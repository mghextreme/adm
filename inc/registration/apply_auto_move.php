<?php
	include('../functions.php');
	connectDatabase();
	
	$db = getDatabase($_POST['menu']);
	$field = $_POST['field'];
	$ascdesc = $_POST['ascdesc'];
	$list = NULL;
	
	$sql = "SELECT `id` FROM `{$db}` ORDER BY `{$field}` {$ascdesc}";
	$query = mysql_query($sql);
	while($row = mysql_fetch_assoc($query))
		$list[] = $row['id'];
	
	for ($i = 0; $i < count($list); $i++)
	{
		$sql = "UPDATE `{$db}` SET `order`={$i} WHERE `id`=" . $list[$i];
		$query = mysql_query($sql);
	}
	
	die('ok');
	//die('count = ' . $count);
?>