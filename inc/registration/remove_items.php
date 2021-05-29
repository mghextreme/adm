<?php
	if (isset($_POST['imagecheck']))
	{
		include('../functions.php');
		include('../configuration.php');
		connectDatabase();
		$albumID = $_POST['albumID'];
		
		for ($i = 0; $i < count($_POST['imagecheck']); $i++)
		{
			if (file_exists($absolute_path . "/" . $_POST['imagecheck'][$i]))
				unlink($absolute_path . "/" . $_POST['imagecheck'][$i]);
			
			$sql = "DELETE FROM `albuns` WHERE `link`='" . $_POST['imagecheck'][$i] . "' LIMIT 1";
			mysql_query($sql);
		}
		
		$sql = "SELECT * FROM `albuns` WHERE `id`=" . $albumID;
		$query = mysql_query($sql);
		if ($query == FALSE || mysql_num_rows($query) == 0)
		{
			$sql = "SELECT `columnname` FROM `" . $_POST['db'] . "fields` WHERE `order`=" . $_POST['order'];
			$rows = mysql_fetch_assoc(mysql_query($sql));
			$column = $rows['columnname'];
			
			$sql2 = "UPDATE `" . $_POST['db'] . "` SET `" . $column . "`=NULL WHERE `" . $column . "`=" . $albumID;
			mysql_query($sql2);
		}
		
		die("ok");
	}
	else die("none");
?>