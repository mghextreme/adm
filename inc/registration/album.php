<?php
	include('../configuration.php');
	include('../functions.php');
	connectDatabase();
	
	$db = $_GET['db'];
	$order = $_GET['order'];
	
	if (isset($_GET['id']))
		$id = $_GET['id'];
	else
	{
		if ($_GET['itemid'] >= 0)
		{
			$sql = "SELECT `columnname` FROM `{$_GET['db']}fields` WHERE `order`=" . $_GET['order'];
			$rows = mysql_fetch_assoc(mysql_query($sql));
			$name = $rows['columnname'];
			
			$sql = "SELECT `type` FROM `{$menu_db}` WHERE `database`='{$db}'";
			$rows = mysql_fetch_assoc(mysql_query($sql));
			$idspec = $rows['type'] == 'singleregistration' ? '' : " WHERE `id`=" . $_GET['itemid'];
			
			$sql = "SELECT `{$name}` FROM " . $_GET['db'] . $idspec;
			$rows = mysql_fetch_assoc(mysql_query($sql));
			$value = $rows[$name];
			
			if ($value == FALSE || $value == NULL)
				$id = 0;
			else $id = $value;
		}
		else $id = -$order;
	}
?>
<html>
	<head>
		<link href="../../css/style.css" rel="stylesheet" type="text/css" />
		<script type="text/javascript" src="../../js/validator/jquery-1.5.1.min.js"></script>
		
		<script language="JavaScript">
			function RemoveItems() {
				$.post("remove_items.php", $("#albumForm").serialize(), function(result)
				{
					if(result == 'ok')
					{
						location.reload(true);
					}
					else if (result == 'none')
					{
						document.getElementById('nothing').style.display = "block";
						setTimeout("document.getElementById('nothing').style.display = 'none';", 3000);
					}
					else alert(result);
				});
			}
			
			function Confirm(appear) {
				if (appear)
				{
					document.getElementById('confirm').style.display = "block";
					setTimeout("document.getElementById('confirm').style.display = 'none';", 5000);
					setTimeout("document.getElementById('nothing').style.display = 'none';", 5000);
				}
				else
				{
					document.getElementById('nothing').style.display = 'none';
					document.getElementById('confirm').style.display = 'none';
				}
			}
			
			function SetCover(imageLink) {
				$.post("set_cover.php", { albumID: <?php echo $id; ?>, link: imageLink }, function(result)
				{
					if(result == 'ok')
					{
						location.reload(true);
					}
					else alert(result);
				});
			}
			
			function RemoveSingle(imageLink) {
				$.post("remove_single.php", { albumID: <?php echo $id; ?>, link: imageLink, db: '<?php echo $db; ?>', order: '<?php echo $order; ?>' }, function(result)
				{
					if(result == 'ok')
					{
						location.reload(true);
					}
					else alert(result);
				});
			}
			
			function Rename(imageLink, imageName) {
				$.post("rename.php", { albumID: <?php echo $id; ?>, link: imageLink, name: imageName }, function(result)
				{
					if(result != 'ok')
						alert(result);
				});
			}
		</script>
	</head>
	<body>
		<form id="albumForm" name="albumForm" method="post">
			<input type="hidden" name="albumID" value="<?php echo $id; ?>" id="albumID" />
			<input type="hidden" name="db" value="<?php echo $_GET['db']; ?>" />
			<input type="hidden" name="order" value="<?php echo $_GET['order']; ?>" />
			<?php
				//getAlbumImages($albumId)
				$albumImages = getAlbumItems($id);
				if (count($albumImages) < 1)
				{
					echo '<div id="albumError">Não há itens cadastrados</div>';
				}
				else
				{
					echo '<ul id="albumImages">';
					for($j = 0; $j < count($albumImages); $j++)
					{
						$coverClass = ($albumImages[$j]['cover'] == true || $albumImages[$j]['cover'] == true) ? ' class="selected"' : '' ;
						switch($albumImages[$j]['extension'])
						{
							case 'png':
							case 'bmp':
							case 'gif':
							case 'jpg':
							case 'jpeg':
							case 'tif':
							case 'tiff':
								$image = '<img src="' . $website_link . 'image.php?r=204&g=204&b=204&a=0&wi=200&he=150&st=f&of=f&lk=' . $albumImages[$j]['link'] . '" />';
								break;
							case 'mid':
							case 'mp3':
							case 'mpa':
							case 'ra':
							case 'wav':
							case 'wma':
								$image = '<img src="../../imgs/file_audio.jpg" />';
								break;
								/*
							case '3g2':
							case 'avi':
							case 'flv':
							case 'mov':
							case 'mp4':
							case 'mpg':
							case 'rm':
							case 'swf':
							case 'wmv':
								$image = '<img src="../../imgs/" />';
								break;
								*/
							case 'pdf':
							case 'doc':
							case 'docx':
							case 'txt':
								$image = '<img src="../../imgs/file_doc.jpg" />';
								break;
							case 'xls':
							case 'xlsx':
								$image = '<img src="../../imgs/file_table.jpg" />';
								break;
							default:
								$image = '<img src="../../imgs/file_unknow.jpg" />';
								break;
						}
						echo '<li>
							' . $image . '
							<div title="Selecionar" id="check"><input type="checkbox" name="imagecheck[]" value="' . $albumImages[$j]['link'] . '" id="check" class="imagecheck" /></div>
							<a title="Tornar Capa" href="javascript: SetCover(' . "'" . $albumImages[$j]['link'] . "'" . ');"><div id="cover"' . $coverClass . '></div></a>
							<a title="Excluir" href="javascript: RemoveSingle(' . "'" . $albumImages[$j]['link'] . "'" . ');"><div id="remove"></div></a>
							<textarea id="name" onblur="Rename(' . "'" . $albumImages[$j]['link'] . "', this.value" . ');">' . $albumImages[$j]['name'] . '</textarea>
						</li>';
					}
					echo '</ul>';
					echo '<div id="main">
						Itens: <b>' . count($albumImages) . '</b><br />';
					//<input type="button" name="checkButton" value="Selecionar Todos" id="checkButton" onClick="Check(' . "document.albumForm.imagecheck[]"  . ');" /> <img src="../../imgs/select.png" alt="" /><br />
					echo '<a href="album_order.php?db=' . $_GET['db'] . '&order=' . $_GET['order'] . '&id=' . $id . '&itemid=' . $_GET['itemid'] . '">Ordenar itens</a><br />
						<a href="javascript: Confirm(true);">Excluir selecionados <img src="../../imgs/remove_s.png" /></a><br />
						<div id="confirm"><a href="javascript: RemoveItems();">Confirmar</a> | <a href="javascript: Confirm(false);">Cancelar</a></div>
						<div id="nothing">Nenhum Item Selecionado</div>
						</div>';
					echo '<div class="clearfix"></div>';
				}
			?>
		</form>
	</body>
</html>