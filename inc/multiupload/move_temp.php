<?php
	include('../functions.php');
	connectDatabase();
	$db = $_GET['db'];
	$id = $_GET['id'];
	$itemOrder = $_GET['itemOrder'];
	$albumID = 0;
	$jStart = 0;
	
	$idspec = $id == 0 ? '' : ' WHERE `id`=' . $id;
	
	//Get column from db and set $columnname
	$sql = "SELECT `columnname` FROM `" . $db . "fields` WHERE `order`=" . $itemOrder;
	$rows = mysql_fetch_assoc(mysql_query($sql));
	$columnname = $rows['columnname'];
	
	//Get or Set item albumID
	$sql = "SELECT `{$columnname}` FROM `{$db}`{$idspec}";
	$rows = mysql_fetch_assoc(mysql_query($sql));
	if ($rows[$columnname] == 0 || $rows[$columnname] == NULL)
	{
		if ($id >= 0)
		{
			$number = 0; 
			$sql2 = "SELECT DISTINCT `id` FROM `albuns` ORDER BY `id` DESC";
			$rows2 = mysql_fetch_array(mysql_query($sql2));
			if (count($rows2) > 0)
			{ $number = $rows2[0]; }
			$sql3 = "SELECT DISTINCT `{$columnname}` FROM `{$db}` ORDER BY `{$columnname}` DESC";
			$rows3 = mysql_fetch_array(mysql_query($sql3));
			if (count($rows3) > 0 && $rows3[0] > $number)
			{ $number = $rows3[0]; }
			if ($number != 0)
			{ $albumID = $number + 1; }
			else $albumID = 1;
			
			$sql4 = "UPDATE `{$db}` SET `{$columnname}`={$albumID}" . $idspec;
			mysql_query($sql4);
		}
		else $albumID = -$itemOrder;
	}
	else
	{
		$albumID = $rows[$columnname];
		$sql = "SELECT `order` FROM `albuns` WHERE `id`='{$albumID}' ORDER BY `order`,`id` DESC LIMIT 1";
		$rows = mysql_fetch_assoc(mysql_query($sql));
		$jStart = $rows['order'] + 1;
	}
	
	//Create Variable with Temp items
	$sql = "SELECT * FROM `temp`";
	$query = mysql_query($sql);
	$items = NULL;
	$i = 0;
	while ($rows = mysql_fetch_assoc($query))
	{
		$items[$i] = $rows;
		$i++;
	}
	
	//Insert Variable (from Temp) into database
	for ($j = 0; $j < $i; $j++)
	{
		$sql = "INSERT INTO `albuns`(`id`, `name`, `link`, `extension`, `order`) VALUES ('" . strval($albumID) . "', '" . $items[$j]['name'] . "', '" . $items[$j]['link'] . "', '" . $items[$j]['extension'] . "', '" . ($jStart + $j) . "');\n";
		mysql_query($sql);
	}
	
	//Delete Temp items
	$sql = 'TRUNCATE TABLE `temp`';
	mysql_query($sql);
	
	die(strval($albumID));
?>