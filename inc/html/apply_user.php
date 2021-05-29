<?php
	session_start();
	include('../functions.php');
	include('../configuration.php');
	connectDatabase();
	
	$sql = "SELECT `name` FROM `{$users_db}` WHERE `login`='" . $_POST['login'] . "'";
	$query = mysql_query($sql);
	if ($query != FALSE && mysql_num_rows($query) > 0 && $_POST['login'] != $_SESSION['usere2'])
	{
		$name = mysql_fetch_assoc($query);
		die('exist-' . $name['name']);
	}
	else
	{
		$id = $_POST['id'];
		$time = GetTime();
		
		$password = '';
		if (isset($_POST['password']) && strlen($_POST['password']) > 4)
			$password = ", `password`='" . crypt($_POST['password']) . "'";
		
		$sql = "SELECT `id` FROM `{$users_db}` WHERE `login`='{$_SESSION['usere2']}'";
		$itemid = mysql_fetch_assoc(mysql_query($sql));
		
		$sql = "UPDATE `{$users_db}` SET `modificationdate`='{$time['sql']}', `modificationuser`={$itemid['id']}, `name`='{$_POST['name']}', `email`='{$_POST['email']}', `login`='{$_POST['login']}'{$password} WHERE `id`=" . $id;
		mysql_query($sql);
		
		$_SESSION['usere2'] = $_POST['login'];
		
		die('ok');
	}
?>