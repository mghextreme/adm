<?php
	$sql = "SELECT subcategories FROM `config`";
	$query = mysql_query($sql);
	$result = mysql_fetch_assoc($query);
	if ($result['subcategories'] > 0)
			$sub = true;
	else $sub = false;
?>
<script type="text/javascript" charset="utf-8">
	function Update(formID) {
		$.post('inc/categories/apply_change.php', $('ul#categoriesMenu').find('li#' + formID).find('form').serialize(), function(result) {
			if (result != 'ok') {
				alert(result);
			}
		});
	}
	function UpdateAll() {
		var items = $('ul#categoriesMenu').find('li').find('form');
		for (var i = 0; i < items.length; i++)
		{
			Update(items[i].id);
		}
	}
	function AddSub(classname) {
		var count = $("ul." + classname).find('li').size();
		$("ul." + classname).append('<li class="' + count + '"><input type="text" name="subcategory[]" class="text2" value="" /><a href="javascript: RemoveSub(' + classname + ', ' + count + ')" title="Remover"><img class="remove" src="imgs/remove_s.png" height="20" width="20" /></a></li>');
	}
	function RemoveSub(ulclass, liclass) {
		$('ul.' + ulclass).find('li.' + liclass).remove();
	}
	function AddCat(classname) {
		var id;
		$.post('inc/categories/add_category.php', function(result) {
			id = result;
		
			var sub = <?php echo $sub == 1 ? 'true' : 'false'; ?>;
			var count = $("ul#categoriesMenu").find('li').size();
			var appendText = '<li id="' + count + '">';
			appendText += '<form id="' + count + '" class="category">';
			appendText += '<input type="text" name="title" class="text" value="Digite um nome" />';
			appendText += '<input type="hidden" name="category" value="' + id + '" />';
			appendText += '<input type="button" value="Salvar" id="submit" onclick="Update(' + count + ');" />';
			if (sub)
				appendText += '<input type="button" value="+ Subcategoria" id="newsub" onclick="AddSub(' + count + ')" />';
			appendText += '<a href="javascript: RemoveCat(' + count + ')" title="Remover"><img class="remove" src="imgs/remove_s.png" height="20" width="20" /></a>';
			
			if (sub)
			{
				appendText += '<ul id="subcategoriesMenu" class="' + count + '">';
				appendText += '</ul>';
			}
			
			appendText += '</form></li>';
			$("ul#categoriesMenu").append(appendText);
		});
	}
	function AddOverDiv(content) {
		$('body').append('<div id="OverflowBGDiv" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background-color: black; opacity: 0.5; z-index: 1000;" onclick="RemoveOverDiv();"></div>');
		var left = (window.innerWidth / 2) - 205;
		var top = (window.innerHeight / 2) - 45;
		$('body').append('<div id="OverflowContentDiv" style="position: fixed; top: ' + top + 'px; left: ' + left + 'px; width: 400px; height: 60px; border: 10px solid #ccc; z-index: 1005; background-color: white; font-size: 15px; padding: 10px;"></div>');
		if (content != 0)
		{
			$('body').find('#OverflowContentDiv').append(content);
		}
	}
	function RemoveOverDiv() {
		$('body').find('div#OverflowBGDiv').remove();
		$('body').find('div#OverflowContentDiv').remove();
	}
	function ConfirmRemove(formID)
	{
		AddOverDiv('Tem certeza de que deseja remover esta categoria?<br/><br/><a href="javascript: RemoveCat(' + formID + ');"> Remover </a> | <a href="javascript: RemoveOverDiv();"> Cancelar </a>');
	}
	function RemoveCat(formID) {
		RemoveOverDiv();
		$.post('inc/categories/remove_category.php', $('ul#categoriesMenu').find('li#' + formID).find('form').serialize(), function(result) {
			if (result != 'ok') {
				alert(result);
			}
		});
		$('ul#categoriesMenu').find('li#' + formID).remove();
	}
</script>
<ul id="categoriesMenu">
	<h1>Categorias:</h1>
	<?php
		$sql = "SELECT * FROM `categories`";
		$query = mysql_query($sql);
		for ($j = 0; $rows = mysql_fetch_assoc($query); $j++)
		{
			$id = $rows['id'];
			echo '<li id="' . $j . '">';
			echo '<form id="' . $j . '" class="category">' . "\n";
			echo '<input type="text" name="title" class="text" value="' . $rows['name'] . '" />' . "\n";
			echo '<input type="hidden" name="category" value="' . $id . '" />' . "\n";
			echo '<input type="button" value="Salvar" id="submit" onclick="Update(' . $j . ');" />';
			if ($sub)
				echo '<input type="button" value="+ Subcategoria" id="newsub" onclick="AddSub(' . $j . ')" />';
			echo '<a href="javascript: ConfirmRemove(' . $j . ')" title="Remover"><img class="remove" src="imgs/remove_s.png" height="20" width="20" /></a>' . "\n";
			
			if ($sub)
			{
				echo '<ul id="subcategoriesMenu" class="' . $j . '">';
				
				$sql2 = "SELECT * FROM `subcategories` WHERE categoryid=" . $id;
				$query2 = mysql_query($sql2);
				
				if (mysql_num_rows($query2) > 0)
				{
					for ($k = 0; $rows2 = mysql_fetch_assoc($query2); $k++)
					{
						echo '<li class="' . $k . '">';
						echo '<input type="text" name="subcategory[]" class="text2" value="' . $rows2['name'] . '" />' . "\n";
						echo '<a href="javascript: RemoveSub(' . $j . ',' . $k . ')" title="Remover"><img class="remove" src="imgs/remove_s.png" height="20" width="20" /></a>' . "\n";
						echo '</li>';
					}
				}
				echo '</ul>';
			}
			echo '</form>';
			echo '</li>';
		}
	?>
</ul>
<div id="left">
	<input type="button" value="+ Categoria" onclick="AddCat()" id="updateAll" />
	<input type="button" value="Salvar Todos" onclick="UpdateAll()" id="updateAll" />
	<input type="button" value="Cancelar" onclick="window.location.reload();" id="updateAll" />
</div>