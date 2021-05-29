<script type="text/javascript" charset="UTF-8" language="JavaScript">
	jQuery(document).ready(function(){
		jQuery('#submit.submit').click(function(){
			jQuery("#theForm.theForm").validationEngine();
		});
	});
	
	function ConfirmEdit(){
		$.post('inc/banners/apply_edit.php', $('#theForm').serialize(), function(result){
			if (result == 'ok')
			{
				window.location.replace('index.php?menu=banners');
			}
			else alert(result);
		});
	}
</script>
<form name="theForm" id="theForm" method="post" class="theForm" action="javascript: ConfirmEdit();">
	<?php
		$sql = "SELECT `name`, `href`, `link` FROM `banners` WHERE `id`=" . $_GET['id'];
		$fields = mysql_fetch_assoc(mysql_query($sql));
		
		echo '<input type="hidden" name="id" value="' . $_GET['id'] . '">';
		
		echo '<h1>Imagem</h1>';
		echo '<div id="bannerDiv"><img src="' . $website_link . 'image.php?lk=' . $fields['link'] . '&wi=540&he=150&of=f&st=f" alt="" width="540" height="150" /></div>';
		
		echo '<h1>Título</h1>';
		echo '<input class="stringField validate[custom[onlyLetterNumber]]" id="name" type="text" name="name" value="' . $fields['name'] . "\">";
		
		echo '<h1>Link</h1>';
		echo '<input class="stringField" id="href" type="text" name="href" value="' . $fields['href'] . "\">";
		
		echo '<div id="bottom"><a title="Concluir"><input class="submit" id="submit" type="submit" value="" /></a>
			<a href="index.php?menu=banners" title="Cancelar"><img src="imgs/remove.png" /></a>' . "\n";
	?>
</form>