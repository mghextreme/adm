<script type="text/javascript" charset="utf-8">
	function AddOverDiv(content) {
		$('body').append('<div id="OverflowBGDiv" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background-color: black; opacity: 0.5; z-index: 1000;" onclick="RemoveOverDiv();"></div>');
		var left = (window.innerWidth / 2) - 255;
		var top = (window.innerHeight / 2) - 65;
		$('body').append('<div id="OverflowContentDiv" style="position: fixed; top: ' + top + 'px; left: ' + left + 'px; width: 500px; height: 100px; border: 10px solid #ccc; z-index: 1005; background-color: white; font-size: 15px; padding: 10px;"></div>');
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
	function RemoveTable(tablename) {
		RemoveOverDiv();
		$.post('inc/manager/apply_remove_table.php', {table: tablename}, function(result){
			if (result != 'ok')
			{ alert(result); }
			else
			{
				$('ul#tables').find('li#' + tablename).remove();
				ActionDone();
			}
		});
	}
	function ConfirmRemove(tablename) {
		var string = 'Tem Certeza de que deseja remover esta tabela?<br/>';
		string += '(Isto implicará no apagamento de todos os dados existentes em cada item da tabela e na configuração dos campos existentes)<br/><br/>';
		string += '<a href="javascript: RemoveTable(' + "'" + tablename + "'" + ')"> Confirmar </a> | <a href="javascript: RemoveOverDiv()"> Cancelar </a>';
		AddOverDiv(string);
	}
</script>
<ul id="iconsMenu">
	<a href="index.php?menu=manager"><li><img src="imgs/list.png" title="Listar Todas as Tabelas" /></li></a>
	<a href="index.php?menu=manager&submenu=add"><li><img src="imgs/new.png" title="Nova Tabela" /></li></a>
	<a href="index.php?menu=manager&submenu=files"><li><img src="imgs/files.png" title="Gerenciar Arquivos" /></li></a>
	<a href="index.php?menu=manager&submenu=config"><li><img src="imgs/yes_no.png" title="Alterar Definições" /></li></a>
	<a href="index.php?menu=manager&submenu=menu"><li><img src="imgs/menu.png" title="Menu" /></li></a>
</ul>
<div class="clearfix"></div>
<ul id="tables">
	<?php
		$sql = "SELECT * FROM `menu` WHERE `type`='registration' || `type`='singleregistration'";
		$query = mysql_query($sql);
		$link = "index.php?menu=manager";
		while($rows = mysql_fetch_array($query))
		{
			echo '<li id="' . $rows['link'] . '">';
			if ($rows['link'] != 'users')
			{
				echo '<a title="Editar" href="' . $link . '&submenu=edit&link=' . $rows['link'] . '"><img id="edit" src="imgs/edit_s.png" /></a>';
				echo '<a title="Remover" href="javascript: ConfirmRemove(' . "'" . $rows['link'] . "'" . ');"><img id="remove" src="imgs/remove_s.png" /></a>';
				echo '<a title="Ordenar Campos" href="' . $link . '&submenu=order&link=' . $rows['link'] . '"><img id="order" src="imgs/order_v.png" /></a>';
			}
			echo $rows['name'] . " (" . $rows['link'] . ")<br />";
			if ($rows['type'] == 'registration')
			{
				if (countItems($rows['link']) > 0)
					echo "<b>" . countItems($rows['link']) . "</b> ite" . (countItems($rows['link']) > 1 ? 'ns' : 'm');
				else echo '<b>Nenhum</b> item cadastrados';
			}
			echo '</li>';
		}
	?>
</ul>