<script type="text/javascript" src="js/drag/header.js"></script>
<script type="text/javascript" src="js/drag/redips-drag-min.js"></script>
<script type="text/javascript" src="js/drag/script.js"></script>
<script type="text/javascript" charset="UTF-8">
	function Move()
	{
		$.post('inc/registration/apply_move.php', $("form#orderForm").serialize(), function(result)
		{
			if (result == 'ok')
			{ window.location = 'index.php?menu=<?php echo $_GET['menu']; ?>'; }
			else alert(result);
		});
	}
	function AutoOrder()
	{
		$.post('inc/registration/apply_auto_move.php', $("form#autoForm").serialize(), function(result)
		{
			if (result == 'ok')
			{ window.location.reload(); }
			else alert(result);
		});
	}
</script>
<ul id="iconsMenu">
	<?php $menu = $_GET['menu']; ?>
	<a href="index.php?menu=<?php echo $menu; ?>"><li><img src="imgs/list.png" title="Listar Todos" /></li></a>
	<a href="index.php?menu=<?php echo $menu; ?>&submenu=add"><li><img src="imgs/new.png" title="Novo" /></li></a>
</ul>
<div class="clearfix"></div>
<div id="drag">
	<form name="autoForm" id="autoForm" class="autoForm" action="javascript: AutoOrder();">
		<input type="hidden" name="menu" value="<?php echo $menu; ?>" id="list"/>
		Ordenar automaticamente
		<select name="field">
			<option value="creationdate">Data de criação</option>
			<?php
				$sql = "SELECT `name`, `columnname` FROM `{$menu}fields` WHERE `type`!='files' && `type`!='image' && `type`!='password' && `type`!='singleimage' && `type`!='multiple' && `type`!='maps'";
				$query = mysql_query($sql);
				while($row = mysql_fetch_assoc($query))
					echo "<option value=\"{$row['columnname']}\">{$row['name']}</option>";
			?>
		</select>
		ordem
		<select name="ascdesc">
			<option value="ASC">Crescente</option>
			<option value="DESC">Decrescente</option>
		</select>
		<input type="submit" id="submit" class="submit" value="" />
	</form>
	<form name="orderForm" id="orderForm">
		<input type="hidden" name="menu" value="<?php echo $menu; ?>" id="list"/>
		<table id="table">
			<?php
				$menu = $_GET['menu'];
				//getItemsArray($pagelink, $search, $pagenumber, $maxPageItems)
				$items = getItemsArray($menu, '', 0, 0);
				
				if ($items[0] != 0)
				{
					for($i = 0; $i < count($items[0]); $i++)
					{
						if ($i > 0)
							echo '<tr class="mark"><td class="mark"></td></tr>';
						
						echo '<tr class="av"><td class="av"><div class="drag">' . $items[0][$i] . '<input type="hidden" name="list[]" value="' . $items[1][$i] . '" id="list"/></div></td></tr>';
					}
				}
				else echo '<p>Não foram encontrados resultados nessa lista.</p>';
			?>
		</table>
	</form>
	<div class="clearfix"></div>
	<a href="javascript: Move();" class="submit" title="Aplicar Alterações"><img src="imgs/ok.png" width="40" /></a>
</div>