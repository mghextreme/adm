<?php $menu = $_GET['menu']; ?>
<script type="text/javascript" charset="UTF-8">
	function AddOverDiv(content) {
		$('body').append('<div id="OverflowBGDiv" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background-color: black; opacity: 0.5; z-index: 1000;" onclick="RemoveOverDiv();"></div>');
		var left = (window.innerWidth / 2) - 205;
		var top = (window.innerHeight / 2) - 45;
		$('body').append('<div id="OverflowContentDiv" style="position: fixed; top: ' + top + 'px; left: ' + left + 'px; width: 400px; height: 90px; border: 10px solid #ccc; z-index: 1005; background-color: white; font-size: 15px; padding: 10px;"></div>');
		if (content != 0)
		{ $('body').find('#OverflowContentDiv').append(content); }
	}
	function RemoveOverDiv() {
		$('body').find('div#OverflowBGDiv').remove();
		$('body').find('div#OverflowContentDiv').remove();
	}
	function redirect()
	{
		var value = $('.java#searchField').val();
		if (value != 'Pesquisar...' && value != '')
			window.location = "index.php?menu=<?php echo $menu; ?>&search=" + value;
		else window.location = "index.php?menu=<?php echo $menu; ?>";
	}
	function RemoveItem(itemid)
	{
		RemoveOverDiv();
		$.post('inc/registration/apply_remove.php', { id: itemid, page: '<?php echo $_GET['menu']; ?>' }, function(result){
			if(result == 'ok')
			{ window.location.reload(); }
			else alert(result);
		});
	}
	function ConfirmRemove(itemid)
	{ AddOverDiv('Tem certeza de que deseja remover este item?<br/>(Se houver qualquer arquivo relacionado a este item, será removido com a confirmação)<br/><br/><a href="javascript: RemoveItem(' + itemid + ');"> Remover </a> | <a href="javascript: RemoveOverDiv();"> Cancelar </a>'); }
</script>
<ul id="iconsMenu">
	<a href="index.php?menu=<?php echo $menu; ?>"><li><img src="imgs/list.png" title="Listar Todos" /></li></a>
	<a href="index.php?menu=<?php echo $menu; ?>&submenu=add"><li><img src="imgs/new.png" title="Novo" /></li></a>
	<a href="index.php?menu=<?php echo $menu; ?>&submenu=order"><li><img src="imgs/order.png" title="Ordenar" /></li></a>
	<form name="searchForm" id="searchForm" method="post" action="javascript: redirect()">
		<li id="searchSubmit"><input id="submitSearch" type="submit" value="" title="Pesquisar" /></li>
		<?php
			$search = isset($_GET['search']) ? $_GET['search'] : "";
			if ($search != "")
				echo '<li id="searchField"><input name="search" class="java" id="searchField" autocomplete="on" type="text" value="' . $search . '" onClick="if(this.value==\'Pesquisar...\'){this.value=\'\'} else {this.focus(); this.select()};" onBlur="if(this.value==\'\'){this.value=\'Pesquisar...\'};" /><a href="index.php?menu=' . $menu . '" title="Cancelar Pesquisa"><img id="cancel" src="imgs/remove_s.png" /></a></li>';
			else echo '<li id="searchField"><input name="search" class="java" id="searchField" autocomplete="on" type="text" value="Pesquisar..." onClick="if(this.value==\'Pesquisar...\'){this.value=\'\'} else {this.focus(); this.select()};" onBlur="if(this.value==\'\'){this.value=\'Pesquisar...\'};" /></li>';
		?>
	</form>
</ul>
<div class="clearfix"></div>
<ul id="itemsMenu">
	<?php		
		$page = 1;
		if (isset($_GET['page']))
			$page = $_GET['page'];
		
		$items = getItemsArray($menu, $search, $page, 6);
		
		if ($items[0] != 0)
		{
			for($i = 0; $i < count($items[0]); $i++)
			{
				echo '<div><a href="index.php?menu=' . $menu . '&submenu=edit&id=' . $items[1][$i] . '"><li>' . $items[0][$i] . '</li></a>
				<a href="javascript: ConfirmRemove(' . $items[1][$i] . ');"><img id="remove" src="imgs/remove_s.png" height="20" width="20" /></a>
				<a href="index.php?menu=' . $menu . '&submenu=edit&id=' . $items[1][$i] . '"><img id="edit" src="imgs/edit_s.png" height="20" width="20" /></a></div>';
			}
		}
		else
		{
			if ($search != '')
				echo '<p>Não foram encontrados resultados para esta pesquisa.</p>';
			else echo '<p>Não foram encontrados resultados nessa lista.</p>';
		}
	?>
</ul>
<?php
	$link = 'index.php?menu=' . $menu;
	if ($search != "")
		$link .= '&search=' . $search;
	
	addPager($page, $items[2], $link);
?>