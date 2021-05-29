<?php
	include('../functions.php');
	connectDatabase();
	
	$html = '<select id="value" name="value" onchange="CheckType(' . $_POST['number'] . ');">';
	$sql2 = "SELECT * FROM `fieldtypes` WHERE `value`!='multiple' && `value`!='singleimage' && `value`!='image' && `value`!='category' && `value`!='files' && `value`!='options' && `value`!='password' ORDER BY `name` ASC";
	$query2 = mysql_query($sql2);
	for ($j = 0; $rows2 = mysql_fetch_array($query2); $j++)
	{
		$sel = $_POST['table'] == 'true' ? ($rows2['value'] == 'table' ? ' selected' : '') : ($rows2['value'] == $_POST['value'] ? ' selected' : '');
		$html .= '<option value="' . $rows2['value'] . "\"{$sel}>" . $rows2['name'] . '</option>';
	}
	$html .= '</select>';
	
	if ($_POST['table'] == 'true')
	{
		$sql = "SELECT * FROM `menu` WHERE `type`='registration' && `link`!='users' && `link`!='{$_POST['link']}' ORDER BY `name` ASC";
		$query = mysql_query($sql);
		
		$html .= '<select name="value2" id="value2" style="width: 80px;">';
		
		while($rows = mysql_fetch_assoc($query))
			$html .= '<option value="' . $rows['link'] . '">' . $rows['name'] . '</option>';
		
		$html .= '</select>';
	}
	
	die($html);
?>