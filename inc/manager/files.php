<?php
	$sql = "SELECT * FROM `albuns`";
	$query = mysql_query($sql);
	$items = NULL;
	while ($row = mysql_fetch_assoc($query))
	{
		$items[$row['extension']][] = $row;
	}
?>