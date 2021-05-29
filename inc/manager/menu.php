<script type="text/javascript" src="js/drag/header.js"></script>
<script type="text/javascript" src="js/drag/redips-drag-min.js"></script>
<script type="text/javascript" src="js/drag/script.js"></script>
<script type="text/javascript" charset="utf-8">
	function SubmitForm() {
		$.post('inc/manager/apply_menu_change.php', $('#theForm').serialize(), function(result){
			if (result != 'ok')
			{ alert(result); }
			else window.location = 'index.php?menu=manager';
		});
	}
</script>
<form method="post" action="" class="theForm" id="theForm" name="theForm">
	<input type="hidden" name="db" value="<?php echo $menu_db; ?>" />
	<h1 id="menuTitle">Selecione os campos que um usuário não administrador pode alterar e/ou visualizar:</h1>
	<ul id="menuItemsForm">
		<?php
			$sql = "SELECT * FROM `" . $menu_db . "`";
			$query = mysql_query($sql);
			
			while($rows = mysql_fetch_array($query))
			{
				if ($rows['type'] != 'home' && $rows['type'] != 'logout' && $rows['link'] != 'users' && $rows['type'] != 'manager')
				{
					$checked = $rows['isadmin'] == 1 ? '' : ' checked';
					echo '<li><input name="admin[]" type="checkbox" value="' . $rows['link'] . '"' . $checked . ' />' . $rows['name'] . '</li>';
				}
			}
		?>
	</ul>
	<div id="bottom">
		<a title="Aplicar Alterações"><input id="submit" class="submit" type="button" value="" onclick="SubmitForm()" /></a>
		<a href="index.php?menu=manager" title="Cancelar"><img src="imgs/remove.png" /></a>
	</div>
</form>