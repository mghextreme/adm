<?php
	include('../functions.php');
	connectDatabase();
	
	$id = $_POST['category'];
	$title = $_POST['title'];
	
	$sql = "UPDATE `categories` SET `name`='" . $title . "' WHERE id=" . $id;
	$query = mysql_query($sql);
	
	$sql = "DELETE FROM `subcategories` WHERE categoryid=" . $id;
	$query = mysql_query($sql);
	
	if (isset($_POST['subcategory']))
	{
		$list = $_POST['subcategory'];
		for ($i = 0; $i < count($list); $i++)
		{
			$sql2 = "INSERT INTO `subcategories`(`categoryid`, `subcategoryid`, `name`) VALUES ('" . $id . "', '" . $i . "', '" . $list[$i] . "')";
			$query = mysql_query($sql2);
		}
	}
	
	die('ok');
?>