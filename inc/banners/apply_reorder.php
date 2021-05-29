<?php
	include('../functions.php');
	connectDatabase();
	
	if (isset($_POST['list']))
	{
		$list = $_POST['list'];
		
		for ($i = 0; $i < count($list); $i++)
		{
			$sql = "UPDATE `banners` SET `order`=" . $i . " WHERE `id`=" . $list[$i];
			$query = mysql_query($sql);
		}
		
		die('ok');
	}
	else die('none');
?>