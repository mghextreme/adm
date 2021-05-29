<?php
	include('../functions.php');
	connectDatabase();
	
	if ($_POST['category'] != 'z')
	{
		$sql = "SELECT * FROM `subcategories` WHERE `categoryid`=" . $_POST['category'];
		$query = mysql_query($sql);
		
		$string = '<option value="z" selected></option>';
		while($rows = mysql_fetch_assoc($query))
		{
			$string .= '<option value="' . $rows['subcategoryid'] . '">' . $rows['name'] . '</option>';
		}
	}
	else $string = '<option value="z" selected></option>';
	
	die($string)
?>