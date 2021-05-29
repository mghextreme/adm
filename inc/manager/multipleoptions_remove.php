<?php
	include('../functions.php');
	connectDatabase();
	
	$db = $_POST['db'];
	$id = $_POST['id'];
	$number = isset($_POST['number']) && $_POST['number'] > 0 ? $_POST['number'] : 0;
	
	//Get and Sets `value`
	$sql = "SELECT `value` FROM `{$db}fields` WHERE `id`=" . $id;
	$rows = mysql_fetch_assoc(mysql_query($sql));
	$value = $rows['value'];
	$count = 0;
	
	//Count Existing Items
	$sql = "SELECT `id` FROM `multipleoptions` WHERE `id`={$value}";
	$query = mysql_query($sql);
	while ($rows = mysql_fetch_row($query))
		$count++;
	
	//Remove Item
	$sql2 = "DELETE FROM `multipleoptions` WHERE `id`={$value} && `number`={$number}";
	$query = mysql_query($sql2);
	
	//Check if not last item to reorder other items
	if ($count > 1)
	{
		if ($number < $count - 1)
		{
			for($i = $number + 1; $i < $count; $i++)
			{
				$sql = "UPDATE `multipleoptions` SET `number`=" . ($i - 1) . " WHERE `id`={$value} && `number`={$i}";
				$query = mysql_query($sql);
			}
		}
	}
	else
	{
		$sql = "UPDATE `{$db}fields` SET `value`=NULL WHERE `id`={$id}";
		mysql_query($sql);
	}
	
	die('ok');
?>