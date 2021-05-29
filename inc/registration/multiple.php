<?php
	function InsertItem($type, $itemValue, $number, $requiredType = '')
	{
		$class = generateClass(1, $requiredType);
		switch ($type)
		{
			case 'url':
			case 'string':
				echo "<input id=\"{$number}\" class=\"stringField {$class}\" type=\"text\" name=\"value[]\" value=\"{$itemValue}\" />";
				break;
			case 'text':
				echo "<textarea id=\"{$number}\" class=\"textField {$class}\" name=\"value[]\">{$itemValue}</textarea>";
				break;
			case 'youtube':
				if ($itemValue != NULL && $itemValue != '')
					$youtubeValue = 'http://www.youtube.com/watch?v=' . $itemValue;
				else $youtubeValue = '';
				echo "<input class=\"shortStringField {$class}\" id=\"{$number}\" type=\"text\" name=\"value[]\" value=\"{$youtubeValue}\" />";
				echo '<input title="Visualizar" class="youtubeButton" type="button" onclick="openMultipleYoutube(' . $number . ');" />';
				break;
			case 'time':
				$timeH = ''; $timeM = ''; $timeS = '';
				if ($itemValue != NULL && strlen($itemValue) > 6)
				{
					$timeH = substr($itemValue, 0, 2);
					$timeM = substr($itemValue, 3, 2);
					$timeS = substr($itemValue, 6, 2);
				}
				echo '<input type="text" id="timeh' . $number . '" class="timehour validate[required,custom[integer],min[0],max[23]]" name="value[]" maxlength="2" value="' . $timeH . '" /> horas ' . "\n";
				echo '<input type="text" id="timem' . $number . '" class="timemin validate[required,custom[integer],min[0],max[59]]" name="value2[]" maxlength="2" value="' . $timeM . '" /> minutos ' . "\n";
				echo '<input type="text" id="times' . $number . '" class="timesec validate[required,custom[integer],min[0],max[59]]" name="value3[]" maxlength="2" value="' . $timeS . '" /> segundos ' . "\n";
				break;
			case 'date':
				$dateY = ''; $dateM = ''; $dateD = '';
				$months = array('Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro');
				if ($itemValue != NULL && strlen($itemValue) > 8)
				{
					$dateY = substr($itemValue, 0, 4);
					$dateM = intval(substr($itemValue, 5, 2));
					$dateD = substr($itemValue, 8, 2);
				}
				echo '<input type="text" id="dated' . $number . '" class="dateday validate[required,custom[integer],min[1],max[31]]" name="value[]" maxlength="2" value="' . $dateD . '" /> de ' . "\n";
				echo '<select id="datem' . $number . '" class="datemonth validate[required]" name="value2[]">';
				for($j = 1; $j <= 12; $j++)
				{
					$sel = $j == $dateM ? ' selected' : '';
					echo "<option value=\"{$j}\"{$sel}>{$months[$j - 1]}</option>\n";
				}
				echo '</select> de ' . "\n";
				echo '<input type="text" id="datey' . $number . '" class="dateyear validate[required,custom[integer],min[1900],max[3000]]" name="value3[]" maxlength="4" value="' . $dateY . '" />' . "\n";
				break;
			case 'datetime':
				$dateY = ''; $dateM = ''; $dateD = ''; $timeH = ''; $timeM = ''; $timeS = '';
				$months = array('Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro');
				if ($itemValue != NULL && strlen($itemValue) > 8)
				{
					$dateY = substr($itemValue, 0, 4);
					$dateM = intval(substr($itemValue, 5, 2));
					$dateD = substr($itemValue, 8, 2);
					$timeH = substr($itemValue, 11, 2);
					$timeM = substr($itemValue, 14, 2);
					$timeS = substr($itemValue, 17, 2);
				}
				echo '<input type="text" id="dated' . $number . '" class="dateday validate[required,custom[integer],min[1],max[31]]" name="value[]" maxlength="2" value="' . $dateD . '" /> de ' . "\n";
				echo '<select id="datem' . $number . '" class="datemonth validate[required]" name="value2[]">';
				for($j = 1; $j <= 12; $j++)
				{
					$sel = $j == $dateM ? ' selected' : '';
					echo "<option value=\"{$j}\"{$sel}>{$months[$j - 1]}</option>\n";
				}
				echo '</select> de ' . "\n";
				echo '<input type="text" id="datey' . $number . '" class="dateyear validate[required,custom[integer],min[1900],max[3000]]" name="value3[]" maxlength="4" value="' . $dateY . '" />,' . "\n";
				echo '<input type="text" id="timeh' . $number . '" class="timehour validate[required,custom[integer],min[0],max[23]]" name="value4[]" maxlength="2" value="' . $timeH . '" /> h ' . "\n";
				echo '<input type="text" id="timem' . $number . '" class="timemin validate[required,custom[integer],min[0],max[59]]" name="value5[]" maxlength="2" value="' . $timeM . '" /> min ' . "\n";
				echo '<input type="text" id="times' . $number . '" class="timesec validate[required,custom[integer],min[0],max[59]]" name="value6[]" maxlength="2" value="' . $timeS . '" /> seg ' . "\n";
				break;
			default:
				if (substr($type, 0, 5) == 'table')
				{
					$table = substr($type, 6);
					$type = 'table';
					
					connectDatabase();
					echo '<select id="table' . $number . '" name="value[]" class="tableSelect ' . $class . '">';
					$sql2 = "SELECT `id`, `name` FROM `{$table}` ORDER BY `name` ASC";
					$query2 = mysql_query($sql2);
					while ($rows2 = mysql_fetch_assoc($query2))
					{
						$selected = $rows2['id'] == $itemValue ? ' selected' : '';
						echo "<option value=\"" . $rows2['id'] . "\"{$selected}>" . $rows2['name'] . "</option>";
					}
					echo '</select>';
				}
				break;
		}
	}
	
	include('../configuration.php');
	include('../functions.php');
	connectDatabase();
	
	$db = $_GET['db'];
	$order = $_GET['order'];
	
	$sql = "SELECT `type` FROM `{$menu_db}` WHERE `database`='{$db}'";
	$rows = mysql_fetch_assoc(mysql_query($sql));
	$idspec = $rows['type'] == 'singleregistration' ? '' : " WHERE `id`=" . $_GET['itemid'];
	
	$sql = "SELECT `id`, `value`, `requiredtype` FROM `{$_GET['db']}fields` WHERE `order`=" . $order;
	$rows = mysql_fetch_assoc(mysql_query($sql));
	$rowtype = $rows['value'];
	$reqtype = $rows['requiredtype'];
	
	$sql2 = "SELECT `value`, `fullid` FROM `{$_GET['db']}{$rows['id']}`" . $idspec;
	$query = mysql_query($sql2);
	$values = NULL;
	while ($rows = mysql_fetch_assoc($query))
	{ $values[] = $rows; }
?>
<html>
	<head>
		<title>Área administrativa | E2 - Campo múltiplo</title>
		<link href="../../css/style.css" rel="stylesheet" type="text/css" />
		<script type="text/javascript" src="../../js/validator/jquery-1.5.1.min.js"></script>
		
		<!-- Fancybox Start -->
		<script type="text/javascript" src="../../js/fancybox/jquery.fancybox-1.3.4.pack.js"></script>
		<link rel="stylesheet" type="text/css" href="../../js/fancybox/jquery.fancybox-1.3.4.css" media="screen" />
		<!-- Fancybox End -->
		
		<!-- Validation Engine Start -->
		<script type="text/javascript" src="../../js/validator/jquery.validationEngine-pt.js"></script>
		<script type="text/javascript" src="../../js/validator/jquery.validationEngine.js"></script>
		<script type="text/javascript" src="../../js/validator/finalized.js"></script>
		
		<link href="../../js/validator/validationEngine.jquery.css" rel="stylesheet" type="text/css" />
		<!-- Validation Engine End -->
		
		<script language="JavaScript">
			jQuery(document).ready(function(){
				jQuery('#submit.submit').click(function(){
					jQuery("#multipleForm").validationEngine();
				});
			});
			
			function AddNew() {
				if ($('div#multipleitems.multipleForm p#none').size() > 0)
				{ $('div#multipleitems.multipleForm p#none').remove(); }
				
				var trmany = $('div#multipleitems.multipleForm').find('div');
				var trmanynumber = 0;
				for (var i = 0; i < trmany.size(); trmanynumber++)
				{
					if ($('div#multipleitems.multipleForm div#' + trmanynumber).size() != 0)
					{ i++; }
				}
				$.post("add_multiple_item.php", { requiredtype: '<?php echo $reqtype; ?>', type: '<?php echo $rowtype; ?>', number: trmanynumber }, function(result)
				{
					$('div#multipleitems.multipleForm').append('<div id="' + trmanynumber + '">' + result + '</div>');
				});
			}
			
			function SaveAll() {
				$.post("multiple_save.php", $('#multipleForm').serialize(), function(result)
				{ if (result == 'ok') { location.reload(true); } else { alert(result); } });
			}
			
			function RemoveSingle(number) {
				var fullid = $('div#multipleitems.multipleForm div#' + number).find('input#fullid').val();
				$.post("remove_multiple_item.php", { db: '<?php echo $db; ?>', order: '<?php echo $order; ?>', id: fullid }, function(result)
				{
					if(result == 'ok')
					{ $('div#multipleitems.multipleForm div#' + number).remove(); }
					else alert(result);
				});
			}
			
			function Remove(number) {
				$('div#multipleitems.multipleForm div#' + number).remove();
			}
		</script>
		<?php if ($rowtype == 'youtube') { echo '<script src="../formFunc.js" type="text/javascript" charset="utf-8"></script>'; } ?>
	</head>
	<body>
		<form id="multipleForm" name="multipleForm" method="post" action="javascript: SaveAll()">
			<?php if($idspec!=''){echo "<input type=\"hidden\" name=\"itemID\" value=\"{$_GET['itemid']}\" id=\"itemID\" />";} ?>
			<input type="hidden" name="db" value="<?php echo $_GET['db']; ?>" />
			<input type="hidden" name="order" value="<?php echo $_GET['order']; ?>" />
			<input type="hidden" name="itemid" value="<?php echo $_GET['itemid']; ?>" />
			<div id="multipleitems" class="multipleForm">
				<?php
					if ($values != NULL)
					{
						for($i = 0; $i < count($values); $i++)
						{
							echo "<div id=\"$i\">";
							echo "<input id=\"fullid\" type=\"hidden\" value=\"{$values[$i]['fullid']}\" />";
							InsertItem($rowtype, $values[$i]['value'], $i, $reqtype);
							echo '<a href="javascript: RemoveSingle(' . $i . ')" title="Excluir" id="' . $i . '"><img id="remove" src="../../imgs/remove_s.png" /></a>';
							echo "</div>\n";
						}
					}
					else echo '<p id="none" style="margin-top: 20px; font-size: 20px;"><b>Não há itens cadastrados</b></p>';
				?>
			</div>
			<div id="main">
				Itens: <b><?php echo count($values); ?></b><br />
				<a href="javascript: AddNew();">Adicionar item <img src="../../imgs/plus_s.png" /></a><br />
				<a id="submit" class="submit" href="javascript: document.multipleForm.submit()">Salvar todos <img src="../../imgs/ok_s.png" /></a>
			</div>
			<div class="clearfix"></div>
		</form>
	</body>
</html>