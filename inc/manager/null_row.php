<?php
	include('../functions.php');
	connectDatabase();
	
	$db = $_POST['db'];
	$count = $_POST['many'];
	$text = '';
	
	$text .= '<tr id="' . $count . '"><input id="order" type="hidden" name="order" value="' . $count . '" />';
	$text .= '<td id="name"><textarea name="name"></textarea></td><td id="columnname"><textarea name="columnname"></textarea></td>';
	$text .= '<td id="type"><select id="type" name="type" onchange="CheckType(' . $count . ');">';
	
	$sql = "SELECT * FROM `fieldtypes` ORDER BY `name` ASC";
	$query = mysql_query($sql);
	
	for ($j = 0; $rows = mysql_fetch_array($query); $j++)
	{
		$text .= '<option value="' . $rows['value'] . '">' . $rows['name'] . '</option>';
	}
	
	$text .= '<select></td><td id="required"><input type="checkbox" name="required" value="" /></td>';
	$text .= '<td id="requiredtype"><textarea name="requiredtype"></textarea></td><td id="value"></td>';
	$text .= '<td id="submit"><input title="Salvar" type="button" id="submit" value="" onclick="Confirm(' . "'addnew'," . $count . ')" /></td>';
	$text .= '<td id="cancel"><input title="Excluir" type="button" id="remove" value="" onclick="Confirm(' . "'removenew'," . $count . ')" /></td></tr>';
	
	die($text);
?>