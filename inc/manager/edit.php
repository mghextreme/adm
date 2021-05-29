<?php
	$menu = $_GET['menu'];
	$sql = "SELECT subcategories FROM `config`";
	$query = mysql_query($sql);
	$result = mysql_fetch_assoc($query);
	if ($result['subcategories'] > 0)
			$sub = true;
	else $sub = false;
	
	$multipleText = '<select id="value" name="value">';
	$sql2 = "SELECT * FROM `fieldtypes` WHERE `value`!='multiple' && `value`!='singleimage' && `value`!='image' && `value`!='category' && `value`!='files' && `value`!='options' && `value`!='password' ORDER BY `name` ASC";
	$query2 = mysql_query($sql2);
	for ($j = 0; $rows2 = mysql_fetch_array($query2); $j++)
	{
		$multipleText .= '<option value="' . $rows2['value'] . '">' . $rows2['name'] . '</option>';
	}
	$multipleText .= '</select>';
	$multipleText = str_replace("\n", '', $multipleText);
?>
<script type="text/javascript" charset="utf-8">
	function AddOverDiv(content) {
		$('body').append('<div id="OverflowBGDiv" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background-color: black; opacity: 0.5; z-index: 1000;" onclick="RemoveOverDiv();"></div>');
		var left = (window.innerWidth / 2) - 255;
		var top = (window.innerHeight / 2) - 65;
		$('body').append('<div id="OverflowContentDiv" style="position: absolute; top: ' + top + 'px; left: ' + left + 'px; width: 500px; height: 100px; border: 10px solid #ccc; z-index: 1005; background-color: white; font-size: 15px; padding: 10px;"></div>');
		if (content != 0)
		{
			$('body').find('#OverflowContentDiv').append(content);
		}
	}
	function RemoveOverDiv() {
		$('body').find('div#OverflowBGDiv').remove();
		$('body').find('div#OverflowContentDiv').remove();
	}
	function ActionDone() {
		$('body').append('<img id="EditCheck" src="imgs/check.png" height="40" style="position: fixed; bottom: 10px; right: 10px; opacity: 1;" />');
		setTimeout("$('body img#EditCheck').animate({opacity: 0}, 1500, function() { $('body img#EditCheck').remove(); });", 500);
	}
	function Update(formID) {
		if (CheckMainFields(formID) == 0)
		{
			var tr = $('form#editTable table#edit tbody tr#' + formID);
			var id1 = tr.find('input#itemid').val();
			var name1 = tr.find('td#name textarea').val();
			var columnname1 = tr.find('td#columnname textarea').val();
			var type1 = tr.find('td#type select#type').val();
			var required1 = (tr.find('td#required input:checked').size() > 0) ? 1 : 0;
			var requiredtype1 = tr.find('td#requiredtype textarea').val();
			var value1 = (tr.find('td#value').find('select#value').size() > 0) ? tr.find('td#value').find('select#value').val() : -1;
			var value3 = (tr.find('td#value').find('select#value2').size() > 0) ? tr.find('td#value').find('select#value2').val() : -1;
			$.post('inc/manager/apply_change.php', { db: '<?php echo $_GET['link'] ?>', id: id1, name: name1 , columnname: columnname1 , type: type1 , required: required1 , requiredtype: requiredtype1 , value: value1 , value2: value3 }, function(result){
				if (result != 'ok')
				{ alert(result); }
			});
			RemoveOverDiv();
			ActionDone();
		}
	}
	function UpdateAll() {
		var items = $('form#editTable table#edit tbody').find('tr');
		for (var i = 0; i < items.size(); i++)
		{
			if ($('form#editTable table#edit tbody tr#' + items[i].id).find('input#itemid').size() > 0)
			{ Update(items[i].id); }
			else Confirm('addnew', items[i].id);
		}
		ActionDone();
	}
	function AddRow() {
		var trmany = $('table#edit tbody').find('tr');
		var trmanynumber = 0;
		for(var i = 0; i < trmany.size(); trmanynumber++)
		{
			if($('table#edit tbody tr#' + trmanynumber).size() != 0)
				i++;
		}
		$.post('inc/manager/null_row.php', { db: '<?php echo $_GET['link'] ?>', many: trmanynumber }, function(result) {
			$('table#edit tbody').append(result);
		});
	}
	function RemoveRow(formID) {
		RemoveOverDiv();
		columnorder = formID;
		$.post('inc/manager/apply_remove_row.php', { db: '<?php echo $_GET['link'] ?>', order: columnorder }, function(result) {
			if (result != 'ok')
			{ alert(result); }
			$('form#editTable table#edit tbody tr#' + formID).remove();
		});
		ActionDone();
	}
	function CheckMainFields(formID) {
		var row = $('form#editTable table#edit tbody tr#' + formID);
		var errors = 0;
		if (/^\s*$/.test(row.find('td#name textarea').val()))
		{
			row.find('td#name').attr('style', 'border: 2px solid #9E1D29;');
			errors++;
		}
		else row.find('td#name').removeAttr('style');
		
		if (/^\s*$/.test(row.find('td#columnname textarea').val()))
		{
			row.find('td#columnname').attr('style', 'border: 2px solid #9E1D29;');
			errors++;
		}
		else row.find('td#columnname').removeAttr('style');
		
		return errors;
	}
	function Confirm(action, formID) {
		switch (action)
		{
			case 'add':
				var content = '';
				content = 'Tem Certeza de que deseja atualizar este campo?<br/>(Caso o campo ' + "'Tipo'" + ' tenha sido alterado apagará os itens existentes)<br/><br/>';
				content += '<a href="javascript: Update(' + formID + ')"> Confirmar </a> | <a href="javascript: RemoveOverDiv();"> Cancelar </a>';
				AddOverDiv(content);
				break;
			case 'remove':
				var content = '';
				content = 'Tem Certeza de que deseja remover este campo?<br/>(Isto implicará no apagamento de todos os dados existentes em cada item da tabela)<br/><br/>';
				content += '<a href="javascript: RemoveRow(' + formID + ')"> Confirmar </a> | <a href="javascript: RemoveOverDiv();"> Cancelar </a>';
				AddOverDiv(content);
				break;
			case 'addnew':
				var errors = CheckMainFields(formID);
				if (errors === 0)
				{
					//Add real row
					var tr = $('form#editTable table#edit tbody tr#' + formID);
					var name1 = tr.find('td#name textarea').val();
					var columnname1 = tr.find('td#columnname textarea').val();
					var type1 = tr.find('td#type select#type').val();
					var required1 = (tr.find('td#required input:checked').size() > 0) ? 1 : 0;
					var requiredtype1 = tr.find('td#requiredtype textarea').val();
					var value1 = (tr.find('td#value').find('select#value').size() > 0) ? tr.find('td#value').find('select#value').val() : -1;
					var value3 = (tr.find('td#value').find('select#value2').size() > 0) ? tr.find('td#value').find('select#value2').val() : -1;
					$.post('inc/manager/apply_add_row.php', { db: '<?php echo $_GET['link'] ?>', name: name1 , columnname: columnname1 , type: type1 , required: required1 , requiredtype: requiredtype1 , value: value1 , value2: value3 }, function(result){
						if (!parseInt(result))
						{ alert(result); }
						else
						{
							tr.append('<input type="hidden" id="itemid" name="itemid" value="' + result + '" />');
							tr.find('td#submit').find('input').remove();
							tr.find('td#cancel').find('input').remove();
							tr.find('td#submit').append('<input title="Salvar" type="button" id="submit" value="" onclick="Confirm(' + "'add'," + formID + ')" />');
							tr.find('td#cancel').append('<input title="Excluir" type="button" id="remove" value="" onclick="Confirm(' + "'remove'," + formID + ')" />');
							ActionDone();
						}
					});
				}
				break;
			case 'removenew':
				$('form#editTable table#edit tbody').find('tr#' + formID).remove();
				break;
			default:
		}
	}
	function CheckType(formID) {
		var value = $('form#editTable table#edit tbody tr#' + formID + ' td#type select#type option:selected').val();
		
		if (value == 'table')
		{
			$.post('inc/manager/get_fieldtypes.php', { link: '<?php echo $_GET['link']; ?>' }, function(result){
				$('form#editTable table#edit tbody tr#' + formID).find('td#value').html(result);
			});
		}
		else if (value == 'options')
		{
			$('form#editTable table#edit tbody tr#' + formID).find('td#value').html('Salve e Recarregue');
		}
		else if (value == 'multiple')
		{
			var avalue = $('form#editTable table#edit tbody tr#' + formID).find('td#value').find('select#value').size() > 0 ? $('form#editTable table#edit tbody tr#' + formID).find('td#value').find('select#value').val() : 'string';
			if (avalue == 'table')
			{
				$.post('inc/manager/get_fieldtypes_table.php', { link: '<?php echo $_GET['link']; ?>', number: formID, table: true }, function(result){
					$('form#editTable table#edit tbody tr#' + formID).find('td#value').html(result);
				});
			}
			else
			{
				$.post('inc/manager/get_fieldtypes_table.php', { link: '<?php echo $_GET['link']; ?>', number: formID, table: false, value: avalue }, function(result){
					$('form#editTable table#edit tbody tr#' + formID).find('td#value').html(result);
				});
			}
		}
		else $('form#editTable table#edit tbody tr#' + formID).find('td#value').html('');
	}
	
	$(document).ready(function() {
		$("a.multipleOptions").fancybox({
			'overlayShow'			: true,
			'overlayOpacity'		: 0.75,
			'transitionIn'			: 'fade',
			'transitionOut'			: 'fade',
			'titleShow'				: false,
			'titlePosition'			: 'outside',
			'width'					: 520,
			'height'				: '90%',
			'autoScale'				: true,
			'showCloseButton'		: false,
			'showNavArrows'			: false,
			'enableEscapeButton'	: true,
			'type'					: 'iframe',
			'centerOnScroll'		: true
		});
	});
</script>
<ul id="iconsMenu">
	<a href="index.php?menu=<?php echo $menu; ?>"><li><img src="imgs/order.png" title="Listar Todas as Tabelas" /></li></a>
	<a href="index.php?menu=manager&submenu=order&link=<?php echo $_GET['link'] ?>"><li><img src="imgs/list.png" title="Alterar Ordem dos Campos" /></li></a>
	<a href="javascript: UpdateAll();"><li style="float: right; margin: 0;"><img src="imgs/ok.png" title="Aplicar Alterações" /></li></a>
</ul>
<div class="clearfix"></div>
<?php
	connectDatabase();
	
	$sql = "SELECT `name`, `link` FROM `menu` WHERE `link`='" .$_GET['link'] . "'";
	$rows = mysql_fetch_assoc(mysql_query($sql));
?>
<form id="editTable">
	<table width="204" id="edit">
		<thead>
			<tr><th colspan="8"><?php echo $rows['name'] . ' (' . $rows['link'] . ')'; ?></th></tr>
			<tr>
				<th>Nome</th>
				<th>Coluna</th>
				<th>Tipo</th>
				<th title="Obrigatório">Obgt</th>
				<th>Parâmetros</th>
				<th title="Extra"><img src="imgs/edit_symbol.png" alt="Ext" style="padding: 0 30px;" /></th>
				<th title="Confirmar"><img src="imgs/ok_symbol.png" alt="C" /></th>
				<th title="Cancelar"><img src="imgs/remove_symbol.png" alt="X" /></th>
			</tr>
		</thead>
		<tbody>
			<?php
				$sql = "SELECT * FROM `" . $_GET['link'] . "fields` ORDER BY `order` ASC";
				$query = mysql_query($sql);
				
				for ($i = 0; $rows = mysql_fetch_array($query); $i++)
				{
					echo '<tr id="' . $i . '">' . "\n";
					echo '<input id="order" type="hidden" name="order" value="' . $rows['order'] . '" />' . "\n";
					echo '<input id="itemid" type="hidden" name="itemid" value="' . $rows['id'] . '" />' . "\n";
					echo '<td id="name"><textarea name="name">' . $rows['name'] . '</textarea></td>' . "\n";
					echo '<td id="columnname"><textarea name="columnname"' . ($rows['columnname'] == 'name' ? ' readonly' : '') . '>' . $rows['columnname'] . '</textarea></td>' . "\n";
					echo '<td id="type"><select id="type" name="type" onchange="CheckType(' . $i . ');">' . "\n";
					
					$sql2 = "SELECT * FROM `fieldtypes` ORDER BY `name` ASC";
					$query2 = mysql_query($sql2);
					
					for ($j = 0; $rows2 = mysql_fetch_array($query2); $j++)
					{
						$selected = $rows['type'] == $rows2['value'] ? ' selected' : '';
						echo '<option value="' . $rows2['value'] . '"' . $selected . '>' . $rows2['name'] . '</option>' . "\n";
					}
					
					echo '<select></td>' . "\n";
					$selected = $rows['required'] > 0 ? ' checked' : '';
					echo '<td id="required"><input type="checkbox" name="required" value="' . '"' . $selected . ' /></td>' . "\n";
					echo '<td id="requiredtype"><textarea name="requiredtype">' . $rows['requiredtype'] . '</textarea></td>' . "\n";
					
					if ($rows['type'] == 'options')
						echo '<td id="value"><a class="multipleOptions" href="inc/manager/multipleoptions.php?db=' . $_GET['link'] . '&id=' . $rows['id'] . '"><div id="value"></div></a></td>' . "\n";
					else if ($rows['type'] == 'multiple')
					{
						$table = substr($rows['value'], 0, 5) == 'table' ? substr($rows['value'], 6) : FALSE;
						if ($table != FALSE) { $rows['value'] = 'table'; }
						echo "<td id=\"value\">";
						echo '<select id="value" name="value" onchange="CheckType(' . $i . ');">';
						$sql2 = "SELECT * FROM `fieldtypes` WHERE `value`!='multiple' && `value`!='singleimage' && `value`!='image' && `value`!='category' && `value`!='files' && `value`!='options' && `value`!='password' ORDER BY `name` ASC";
						$query2 = mysql_query($sql2);
						for ($j = 0; $rows2 = mysql_fetch_array($query2); $j++)
						{
							$sel = $rows2['value'] == $rows['value'] ? ' selected' : '';
							echo '<option value="' . $rows2['value'] . "\"{$sel}>" . $rows2['name'] . '</option>';
						}
						echo '</select>';
						
						if ($table != FALSE)
						{
							$sql3 = "SELECT * FROM `menu` WHERE `type`='registration' && `link`!='users' && `link`!='{$_GET['link']}' ORDER BY `name` ASC";
							$query3 = mysql_query($sql3);
							echo '<select name="value2" id="value2" style="width: 80px;">';
							while($rows3 = mysql_fetch_assoc($query3))
							{
								$sel = $table == $rows3['value'] ? ' selected' : '';
								echo '<option value="' . $rows3['link'] . "\"{$sel}>" . $rows3['name'] . '</option>';
							}
							echo '</select>';
						}
						echo "</td>\n\n";
					}
					else if ($rows['value'] != NULL)
					{
						if ($rows['type'] == 'table')
						{
							echo '<td id="value">';
							$sql2 = "SELECT * FROM `menu` WHERE `type`='registration' && `link`!='users' && `link`!='{$_GET['link']}' ORDER BY `name` ASC";
							$query2 = mysql_query($sql2);
							$html = '<select name="value" id="value" style="width: 80px;">';
							
							while($rows2 = mysql_fetch_assoc($query2))
							{
								$html .= '<option value="' . $rows2['link'] . '"';
								if ($rows2['link'] == $rows['value'])
									$html .= ' selected';
								$html .= '>' . $rows2['name'] . '</option>';
							}
							
							$html .= '</select>';
							
							echo $html . '</td>' . "\n";
						}
					}
					else echo '<td id="value"></td>' . "\n";
					
					echo '<td id="submit"><input title="Salvar" type="button" id="submit" value="" onclick="Confirm(' . "'add'," . $i . ')" /></td>' . "\n";
					if ($rows['columnname'] != 'name')
						echo '<td id="cancel"><input title="Excluir" type="button" id="remove" value="" onclick="Confirm(' . "'remove'," . $i . ')" /></td>' . "\n";
					else echo '<td id="cancel"></td>' . "\n";
					echo '</tr>' . "\n";
				}
			?>
		</tbody>
	</table>
</form>
<input type="button" id="addCategory" title="Adicionar Coluna" onclick="AddRow()" />
<div class="clearfix"></div>
<ul id="parameters">
	<h2>Parâmetros:</h2>
	<li><b>e-mail</b> - Somente e-mails</li>
	<li><b>number</b> - Somente números</li>
	<li><b>integer</b> - Somente números inteiros *</li>
	<li><b>letternumber</b> - Somente letras e números *</li>
	<li><b>min[x]</b> - Mínimo de caracteres, sendo 'x' um número inteiro</li>
	<li><b>max[x]</b> - Máximo de caracteres, sendo 'x' um número inteiro (os valores padrão são: Caracteres: 300; Texto: 2000)</li>
	<li><b>radio</b> - Deve ser usado em todos os itens onde o Tipo for Opções</li>
	<li><b>extensions[x,y,z][k]</b> - Extensões de arquivo autorizadas (usado para Arquivos), sendo 'x', 'y' e 'z' as extensões (sem ponto ou espaço, separadas por vírgula) e 'k' o tamanho limite em kBytes (opcional)</li>
	<li><br/>* Espaço incluso</li>
</ul>
