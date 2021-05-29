<?php
	include('../functions.php');
	connectDatabase();

	$sql = "SELECT * FROM `temp`";
	$query = mysql_query($sql);
	$items = NULL;
	$it = 0;
	while ($rows = mysql_fetch_assoc($query))
	{
		$items[$it] = $rows;
		$it++;
	}

	$bannersCount = countItems('banners');
	
	for ($j = 0; $j < $it; $j++)
	{
		$sql = 'INSERT INTO `banners`(`link`, `order`) VALUES (' . "'" . $items[$j]['link'] . "', '" . ($bannersCount + $j) . "');\n";
		mysql_query($sql);
	}

	$sql = 'TRUNCATE TABLE `temp`';
	mysql_query($sql);

	die('ok');
?>