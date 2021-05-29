<?php
	if (isset($_POST['link']))
	{
		include('../functions.php');
		include('../configuration.php');
		connectDatabase();
		$albumID = $_POST['albumID'];
		
		if (file_exists($absolute_path . "/" . $_POST['link']))
		{
			if (!unlink($absolute_path . "/" . $_POST['link']))
				die("error");
		}
		
		$sql = "DELETE FROM `albuns` WHERE link='" . $_POST['link'] . "' LIMIT 1";
		mysql_query($sql);
		
		if (isset($_POST['db']) && isset($_POST['order']))
		{
			$sql = "SELECT * FROM `albuns` WHERE `id`='" . $albumID . "'";
			$query = mysql_query($sql);
			if ($query == false || mysql_num_rows($query) == 0)
			{
				$sql = "SELECT `columnname` FROM `" . $_POST['db'] . "fields` WHERE `order`=" . $_POST['order'];
				$rows = mysql_fetch_assoc(mysql_query($sql));
				$column = $rows['columnname'];
				
				$sql2 = "UPDATE `" . $_POST['db'] . "` SET `" . $column . "`=NULL WHERE `" . $column . "`=" . $albumID;
				mysql_query($sql2);
			}
			
			die("ok");
		}
	}
	else die("none");
?>