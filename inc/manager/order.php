<script type="text/javascript" src="js/drag/header.js"></script>
<script type="text/javascript" src="js/drag/redips-drag-min.js"></script>
<script type="text/javascript" src="js/drag/script.js"></script>
<script type="text/javascript" charset="UTF-8">
	function Move()
	{
		$.post('inc/manager/apply_order.php', $("#orderForm").serialize(), function(result)
		{
			if(result == 'ok')
			{ window.location = 'index.php?menu=manager&submenu=edit&link=<?php echo $_GET['link']; ?>'; }
			else alert(result);
		});
	}
</script>
<ul id="iconsMenu">
	<?php $link = $_GET['link']; ?>
	<a href="index.php?menu=manager"><li><img src="imgs/order.png" title="Listar Todas as Tabelas" /></li></a>
	<a href="index.php?menu=manager&submenu=edit&link=<?php echo $link; ?>"><li><img src="imgs/list.png" title="Editar Campos" /></li></a>
</ul>
<div class="clearfix"></div>
<div id="drag">
	<form name="orderForm" id="orderForm">
		<input type="hidden" name="link" value="<?php echo $link; ?>" id="list"/>
		<table id="table">
			<?php
				$sql = "SELECT * FROM `{$link}fields` ORDER BY `order` ASC";
				$query = mysql_query($sql);
				
				$i = 0;
				while($item = mysql_fetch_assoc($query))
				{
					if ($i > 0)
						echo '<tr class="mark"><td class="mark"></td></tr>';
					echo '<tr class="av"><td class="av"><div class="drag">' . $item['name'] . '<input type="hidden" name="list[]" value="' . $item['columnname'] . '" id="list"/></div></td></tr>';
					$i++;
				}
			?>
		</table>
	</form>
	<div class="clearfix"></div>
	<a href="javascript: Move();" class="submit" title="Aplicar Alterações"><img src="imgs/ok.png" width="40" /></a>
</div>