<?php
	include('../functions.php');
	connectDatabase();
	
	$sql = "SELECT * FROM `menu` WHERE `type`='registration' && `link`!='users' && `link`!='{$_POST['link']}' ORDER BY `name` ASC";
	$query = mysql_query($sql);
	
	$html = '<select name="value" id="value" style="width: 80px;">';
	
	while($rows = mysql_fetch_assoc($query))
		$html .= '<option value="' . $rows['link'] . '">' . $rows['name'] . '</option>';
	
	$html .= '</select>';
	
	die($html);
?>