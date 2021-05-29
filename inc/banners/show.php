<script type="text/javascript" src="js/drag/header.js"></script>
<script type="text/javascript" src="js/drag/redips-drag-min.js"></script>
<script type="text/javascript" src="js/drag/script.js"></script>
<script type="text/javascript" charset="UTF-8">
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
	function ConfirmRemove(itemid)
	{
		AddOverDiv('Tem certeza de que deseja remover este item?<br/><br/><a href="javascript: RemoveItem(' + itemid + ');"> Remover </a> | <a href="javascript: RemoveOverDiv();"> Cancelar </a>');
	}
	function Move()
	{
		$.post('inc/banners/apply_reorder.php', $("#bannerForm").serialize(), function(result)
		{
			if(result == 'ok')
			{ window.location.reload(); }
			else if (result != 'none')
			{ alert(result); }
		});
	}
	function RemoveItem(itemID)
	{
		$.post('inc/banners/apply_remove.php', { id: itemID }, function(result)
		{
			if(result == 'ok')
			{ window.location.reload(); }
			else
			{ alert(result); }
		});
	}
</script>
<div id="drag">
	<form name="bannerForm" id="bannerForm">
		<input type="hidden" name="menu" value="<?php echo $menu; ?>" id="list"/>
		<table id="table">
			<div id="uploadify" style="margin-bottom: 10px;">
				<script type="text/javascript" src="js/jquery.uploadify.js"></script>
				<script type="text/javascript" charset="UTF-8">
					$(document).ready(function() {
						$("#fileUpload").fileUpload({
							'fileDesc': 'Arquivos de Imagem',
							'fileExt': '*.jpg; *.jpeg; *.bmp; *.gif; *.png',
							'buttonText': 'Adicionar',
							'folder': '../uploads/',
							'script': 'inc/multiupload/upload_name.php',
							'uploader': 'inc/multiupload/uploader.swf',
							'checkScript': 'inc/multiupload/check.php',
							'cancelImg': 'inc/multiupload/cancel.png',
							'displayData': 'speed',
							'sizeLimit': 3145728,
							'filesLimit': 10,
							'multi': true,
							'auto': true,
							onAllComplete: function MoveTemp() {
								$.post('inc/multiupload/move_banners.php', function(result){ if(result != 'ok'){ alert(result); }}); setTimeout('window.location.reload()', 500); }
						});
					});
				</script>
				<div id="fileUpload">Problemas com o código</div>
				<p></p>
			</div>
			<?php
				$sql = "SELECT * FROM `banners` ORDER BY `order` ASC";
				$query = mysql_query($sql);
				
				if (mysql_num_rows($query) > 0)
				{
					for($i = 0; $rows = mysql_fetch_assoc($query); $i++)
					{
						if ($i > 0)
							echo '<tr class="mark" id="' . $rows['id'] . '"><td class="mark"></td></tr>';
						
						$title = ($rows['name'] == NULL || strlen($rows['name']) < 3) ? $rows['link'] : $rows['name'];
						echo '<tr id="' . $rows['id'] . '" class="av"><td class="av"><div class="drag">' . $title . '<input type="hidden" name="list[]" value="' . $rows['id'] . '" id="list"/>
							<a href="javascript: ConfirmRemove(' . $rows['id'] . ');"><img id="remove" src="imgs/remove_s.png" height="20" width="20" /></a>
							<a href="index.php?menu=banners&submenu=edit&id=' . $rows['id'] . '"><img id="edit" src="imgs/edit_s.png" height="20" width="20" /></a>
							</div></td></tr>';
					}
				}
				else echo 'Não foram encontrados banners cadastrados';
			?>
		</table>
	</form>
	<div class="clearfix"></div>
	<a href="javascript: Move();" class="submit" title="Aplicar Alterações"><img src="imgs/ok.png" width="40" /></a>
</div>