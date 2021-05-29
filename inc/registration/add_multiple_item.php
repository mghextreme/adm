<?php
	include('../functions.php');
	
	$final = '';
	$number = $_POST['number'];
	$class = generateClass(1, $_POST['requiredtype']);
	switch ($_POST['type'])
	{
		case 'url':
		case 'string':
			$final = "<input id=\"{$number}\" class=\"stringField {$class}\" type=\"text\" name=\"value[]\" value=\"\"/>";
			break;
		case 'text':
			$final = "<textarea id=\"{$number}\" class=\"textField {$class}\" name=\"value[]\"></textarea>";
			break;
		case 'youtube':
			$final = '<input class="shortStringField ' . $class . '" id="' . $number . '" type="text" name="value[]" value="" />';
			$final .= '<input title="Visualizar" class="youtubeButton" type="button" onclick="openMultipleYoutube(' . $number . ');" />';
			break;
		case 'time':
			$final .= '<input type="text" id="timeh' . $number . '" class="timehour validate[required,custom[integer],min[0],max[23]]" name="value[]" maxlength="2" value="" /> horas ' . "\n";
			$final .= '<input type="text" id="timem' . $number . '" class="timemin validate[required,custom[integer],min[0],max[59]]" name="value2[]" maxlength="2" value="" /> minutos ' . "\n";
			$final .= '<input type="text" id="times' . $number . '" class="timesec validate[required,custom[integer],min[0],max[59]]" name="value3[]" maxlength="2" value="" /> segundos ' . "\n";
			break;
		case 'date':
			$months = array('Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro');
			$final .= '<input type="text" id="dated' . $number . '" class="dateday validate[required,custom[integer],min[1],max[31]]" name="value[]" maxlength="2" value="" /> de ' . "\n";
			$final .= '<select id="datem' . $number . '" class="datemonth validate[required]" name="value2[]">';
			for($j = 1; $j <= 12; $j++)
				$final .= "<option value=\"{$j}\">{$months[$j - 1]}</option>\n";
			$final .= '</select> de ' . "\n";
			$final .= '<input type="text" id="datey' . $number . '" class="dateyear validate[required,custom[integer],min[1900],max[3000]]" name="value3[]" maxlength="4" value="" />' . "\n";
			break;
		case 'datetime':
			$months = array('Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro');
			$final .= '<input type="text" id="dated' . $number . '" class="dateday validate[required,custom[integer],min[1],max[31]]" name="value[]" maxlength="2" value="" /> de ' . "\n";
			$final .= '<select id="datem' . $number . '" class="datemonth validate[required]" name="value2[]">';
			for($j = 1; $j <= 12; $j++)
				$final .= "<option value=\"{$j}\">{$months[$j - 1]}</option>\n";
			$final .= '</select> de ' . "\n";
			$final .= '<input type="text" id="datey' . $number . '" class="dateyear validate[required,custom[integer],min[1900],max[3000]]" name="value3[]" maxlength="4" value="" />,' . "\n";
			$final .= '<input type="text" id="timeh' . $number . '" class="timehour validate[required,custom[integer],min[0],max[23]]" name="value4[]" maxlength="2" value="" /> h ' . "\n";
			$final .= '<input type="text" id="timem' . $number . '" class="timemin validate[required,custom[integer],min[0],max[59]]" name="value5[]" maxlength="2" value="" /> min ' . "\n";
			$final .= '<input type="text" id="times' . $number . '" class="timesec validate[required,custom[integer],min[0],max[59]]" name="value6[]" maxlength="2" value="" /> seg ' . "\n";
			break;
		default:
			if (substr($_POST['type'], 0, 5) == 'table')
			{
				$table = substr($_POST['type'], 6);
				$_POST['type'] = 'table';
				
				connectDatabase();
				$final .= '<select id="table' . $number . '" name="value[]" class="tableSelect ' . $class . '">';
				$sql2 = "SELECT `id`, `name` FROM `{$table}` ORDER BY `name` ASC";
				$query2 = mysql_query($sql2);
				while ($rows2 = mysql_fetch_assoc($query2))
					$final .= "<option value=\"" . $rows2['id'] . "\">" . $rows2['name'] . "</option>";
				$final .= '</select>';
			}
			break;
	}
	$final .= '<a href="javascript: Remove(' . $number . ');" title="Excluir"><img id="remove" src="../../imgs/remove_s.png" /></a>';
	die($final);
?>