<?php
	if(isset($_GET['id']) && strlen($_GET['id']) > 0)
	{
		include('../functions.php');
		connectDatabase();
		
		$db = $_GET['db'];
		$id = $_GET['id'];
		$options = NULL;
		
		$sql = "SELECT `value` FROM `{$db}fields` WHERE `id`=" . $id;
		$query = mysql_query($sql);
		
		if ($query != FALSE)
		{
			$rows = mysql_fetch_assoc($query);
			if ($rows['value'] != NULL && strlen($rows['value']) > 0)
				$options = GetMultipleOptions($rows['value']);
		}
	}
?>
<html>
	<head>
		<link href="../../css/style.css" rel="stylesheet" type="text/css" />
		<script type="text/javascript" src="../../js/validator/jquery-1.5.1.min.js"></script>
		<script type="text/javascript" charset="UTF-8">
			function Confirm(typevalue, trvalue)
			{
				switch(typevalue)
				{
					case 'addnew':
						var trmany = $('table#multipleOptions tbody').find('tr');
						var trmanynumber = 0;
						for(var i = 0; i < trmany.size(); trmanynumber++)
						{
							if($('table#multipleOptions tbody tr#' + trmanynumber).size() != 0)
								i++;
						}
						
						var appendText = '<tr id="' + trmanynumber + '">';
						appendText += '<td><input type="text" name="value" id="option" value="Novo valor" /></td>';
						appendText += '<td id="submit"><input title="Salvar" type="button" id="submit" value="" onclick="Confirm(' + "'add'," + trmanynumber + ')" /></td>';
						appendText += '<td id="cancel"><input title="Excluir" type="button" id="remove" value="" onclick="Confirm(' + "'removenew'," + trmanynumber + ')" /></td></tr>';
						
						$('table#multipleOptions tbody').append(appendText);
						break;
					case 'add':
						var value2 = $('table#multipleOptions tbody tr#' + trvalue).find('td input#option').val();
						$.post('multipleoptions_add.php', { db: '<?php echo $db; ?>', id: <?php echo $id; ?>, value: value2 }, function(result){
							if (parseInt(result))
							{
								$('table#multipleOptions tbody tr#' + trvalue).id = result;
								$('table#multipleOptions tbody tr#' + trvalue).append('<input type="hidden" id="number" name="number" value="' + result + '" />')
								$('table#multipleOptions tbody tr#' + trvalue).find('td#submit').remove();
								$('table#multipleOptions tbody tr#' + trvalue).find('td#cancel').remove();
								$('table#multipleOptions tbody tr#' + trvalue).append('<td id="submit"><input title="Salvar" type="button" id="submit" value="" onclick="Confirm(' + "'edit'," + result + ')" /></td>');
								$('table#multipleOptions tbody tr#' + trvalue).append('<td id="cancel"><input title="Excluir" type="button" id="remove" value="" onclick="Confirm(' + "'remove'," + result + ')" /></td></tr>');
							}
							else alert(result);
						});
						break;
					case 'edit':
						var value2 = $('table#multipleOptions tbody tr#' + trvalue).find('td input#option').val();
						var number2 = $('table#multipleOptions tbody tr#' + trvalue).find('input#number').val();
						$.post('multipleoptions_edit.php', { db: '<?php echo $db; ?>', id: <?php echo $id; ?>, number: number2, value: value2 }, function(result){
							if (result != 'ok')
								alert(result);
						});
						break;
					case 'removenew':
						$('table#multipleOptions tbody tr#' + trvalue).remove();
						break;
					case 'remove':
						var value2 = $('table#multipleOptions tbody tr#' + trvalue).find('input#number').val();
						$.post('multipleoptions_remove.php', { db: '<?php echo $db; ?>', id: <?php echo $id; ?>, number: value2 }, function(result){
							if (result == 'ok')
							{ window.location.reload(); }
							else alert(result);
						});
						break;
				}
			}
			
			function UpdateAll()
			{
				jQuery.each($('table#multipleOptions tbody tr'), function(index) {
					if ($('table#multipleOptions tbody').find('tr#' + index).find('input#number').size() > 0)
					{ Confirm('edit', index); alert(index + ' edit;'); }
					else { Confirm('add', index); alert(index + ' add;'); }
				});
			}
		</script>
	</head>
	<body id="multipleOption">
		<form id="multipleOptionsForm" class="multipleOptionsForm" action="" method="post">
			<table id="multipleOptions">
				<thead>
					<tr><th id="valor">Valor</th><th title="Confirmar"><img src="../../imgs/ok_symbol.png" alt="C" /></th><th title="Cancelar"><img src="../../imgs/remove_symbol.png" alt="X" /></th></tr>
				</thead>
				<tbody>
					<?php
						if(count($options) > 0)
						{
							foreach($options as $opt)
							{
								echo "\n<tr id=\"{$opt['number']}\">\n";
								echo '<input type="hidden" name="number" id="number" value="' . $opt['number'] . '" />';
								echo '<td><input type="text" name="value" id="option" value="' . $opt['value'] . "\" /></td>\n";
								echo '<td id="submit"><input title="Salvar" type="button" id="submit" value="" onclick="Confirm(' . "'edit'," . $opt['number'] . ')" /></td>' . "\n";
								echo '<td id="cancel"><input title="Excluir" type="button" id="remove" value="" onclick="Confirm(' . "'remove'," . $opt['number'] . ')" /></td>' . "\n</tr>\n";
							}
						}
					?>
				</tbody>
			</table>
			<div style="margin-top: 10px;">
				<input type="button" value="Novo Valor" onclick="Confirm('addnew', 0)" id="updateAll" />
				<input type="button" value="Salvar Todos" onclick="UpdateAll()" id="updateAll" />
				<input type="button" value="Fechar" onclick="parent.jQuery.fancybox.close();" id="updateAll" />
			</div>
		</form>
	</body>
</html>