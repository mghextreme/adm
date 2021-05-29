<?php
	include('../functions.php');
	connectDatabase();
	$sql = "INSERT INTO `categories`(`name`) VALUES ('" . 'Digite um nome' . "')";
	$query = mysql_query($sql);
	$rows = mysql_fetch_assoc(mysql_query("SELECT `id` FROM `categories` ORDER BY `id` DESC LIMIT 1"));
	die($rows['id']);
?>