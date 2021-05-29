<?php
	include_once ("functions.php");
	include_once ("configuration.php");
	
	connectDatabase();
	$user = str_replace(array('"',"'",'|',"*",' ','=','+','%'), '', $_POST['user']);
	$password = str_replace(array('"',"'",'|',"*",' ','=','+','%'), '', $_POST['pass']);
	
	$sql = "SELECT `login`, `password` FROM `{$users_db}` WHERE `login`='" . $user . "'";
	$query = mysql_query($sql);
	
	if (mysql_num_rows($query) == 0)
		die("error");
	else
	{
		while ($rows = mysql_fetch_array($query, MYSQL_ASSOC))
		{
			if (crypt($password, $rows["password"]) == $rows["password"])
			{
				session_start();
				$_SESSION['usere2'] = $user;
				die("connected");
			}
			else die("error");
		}
	}
?>