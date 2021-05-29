<?php
	include('../functions.php');
	connectDatabase();
	
	$ban = isset($_POST['banners']) ? 1 : 0;
	$cat = isset($_POST['categories']) ? 1 : 0;
	$sub = isset($_POST['subcategories']) ? 1 : 0;
	
	$sql = "UPDATE `config` SET `email`='" . $_POST['email'] . "', `banners`=" . $ban . ", `categories`=" . $cat . ", `subcategories`=" . $sub;
	mysql_query($sql);
	
	die('ok');
?>