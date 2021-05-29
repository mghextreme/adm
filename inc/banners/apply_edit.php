<?php
	include('../functions.php');
	connectDatabase();
	
	$name = $_POST['name'];
	$href = $_POST['href'];
	$id = $_POST['id'];
	
	if (strlen($href) > 0 && $href != '#') {
		$st = substr($href, 0, 7);
		if ($st != 'http://' && $st != 'https:/') {
			$href = 'http://' . $href;
	}	}
	
	$sql = "UPDATE `banners` SET `name`='" . $name . "', `href`='" . $href . "' WHERE `id`=" . $id;
	mysql_query($sql);
	
	die('ok');
?>