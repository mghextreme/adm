<?php
	$sql = "SELECT * FROM `config`";
	$rows = mysql_fetch_assoc(mysql_query($sql));
	$banners = $rows['banners'] == 1 ? true : false;
	$cat = $rows['categories'] == 1 ? true : false;
	$sub = $rows['subcategories'] == 1 ? true : false;
?>
<script type="text/javascript" charset="utf-8">
	function CheckSubcategories(){
		if($('input#categories').is(':checked'))
		{
			$('input#subcategories').removeAttr('disabled');
		}
		else $('input#subcategories').attr('disabled', 'true');
	}
	
	function ApplyConfig(){
		$.post('inc/manager/apply_config.php', $('#theForm').serialize(), function(result){
			if (result != 'ok')
			{ alert(result); }
			else
			{
				window.location = 'index.php?menu=manager';
			}
		});
	}
</script>
<form name="theForm" id="theForm" class="theForm" action="javascript: ApplyConfig()">
	<h1>E-mail do administrador:</h1>
	<input type="text" name="email" value="<?php echo $rows['email']; ?>" id="email" class="stringField" />
	<h1>Ativar Banners:</h1>
	<input type="checkbox" name="banners" value="1" id="banners" <?php if($banners){echo "checked ";} ?>/>
	<h1>Ativar Categorias:</h1>
	<input type="checkbox" name="categories" value="1" id="categories" onchange="CheckSubcategories()" <?php if($cat){echo "checked ";} ?>/>
	<h1>Ativar Subcategorias:</h1>
	<input type="checkbox" name="subcategories" value="1" id="subcategories" <?php if(!$cat){echo 'disabled="true" ';} if($sub){echo "checked ";} ?>/>
	<div id="bottom">
		<input type="submit" name="submit" value="" id="submit" class="submit" title="Aplicar Alterações" />
		<a href="index.php?menu=manager"><img src="imgs/remove.png" alt="" title="Cancelar" /></a>
	</div>
</form>