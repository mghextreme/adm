<?php
	include('../configuration.php');
	include('../functions.php');
	connectDatabase();
	
	$db = $_GET['db'];
	$order = $_GET['order'];
	$id = $_GET['id'];
	$itemid = $_GET['itemid'];
?>
<html>
	<head>
		<link href="../../css/style.css" rel="stylesheet" type="text/css" />
		<script type="text/javascript" src="../../js/validator/jquery-1.5.1.min.js"></script>
		<script type="text/javascript" src="../../js/drag/header.js"></script>
		<script type="text/javascript" src="../../js/drag/redips-drag-min.js"></script>
		<script type="text/javascript" src="../../js/drag/script.js"></script>
		<script language="JavaScript">
			function ApplyOrder() {
				$.post("apply_album_order.php", $("#albumForm").serialize(), function(result)
				{
					if(result == 'ok')
					{ location = 'album.php?<?php echo "id={$id}&db={$db}&order={$order}&itemid={$itemid}"; ?>'; }
					else alert(result);
				});
			}
		</script>
		<style type="text/css" media="screen">
			div#drag {
				list-style: none;
				width: 670px;
				margin-left: 0;
				float: right;
			}
			
			div#drag table#table {
				width: 660px;
			}
			
			div#drag table#table tr.av {
				height: 85px;
			}
			
			div#drag table#table tr.av td.av {
				width: 658px;
				height: 85px;
			}
			
			div#drag table#table tr.av td.av div.drag {
				width: 650px;
				height: 75px;
				padding: 5px;
			}
			
			div#drag table#table tr.av td.av div.drag img {
				width: 100px;
				height: 75px;
				float: left;
			}
			
			div#drag table#table p {
				margin: 5px 0 0 10px;
				float: left;
				text-align: center;
				color: black;
				font: 12px Arial, Helvetica, sans-serif;
			}
		</style>
	</head>
	<body>
		<form id="albumForm" name="albumForm" method="post">
			<input type="hidden" name="albumID" value="<?php echo $id; ?>" id="albumID" />
			<div id="drag">
				<table id="table"><tbody>
						<?php
							//getAlbumImages($albumId)
							$albumImages = getAlbumItems($id);
							for($j = 0; $j < count($albumImages); $j++)
							{
								
								switch($albumImages[$j]['extension'])
								{
									case 'png':
									case 'bmp':
									case 'gif':
									case 'jpg':
									case 'jpeg':
									case 'tif':
									case 'tiff':
										$image = '<img src="' . $website_link . 'image.php?r=204&g=204&b=204&a=0&wi=100&he=75&st=f&of=f&lk=' . $albumImages[$j]['link'] . '" />';
										break;
									case 'mid':
									case 'mp3':
									case 'mpa':
									case 'ra':
									case 'wav':
									case 'wma':
										$image = '<img src="../../imgs/file_audio.jpg" />';
										break;
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
								echo '<tr class="av"><td class="av"><div class="drag">' . $image . '<p>' . $albumImages[$j]['name'] . '</p><input type="hidden" name="link[]" value="' . $albumImages[$j]['link'] . '" /></div></td></tr>';
							}
						?>
				</tbody></table>
			</div>
			<div id="main">
				<a href="javascript: ApplyOrder();">Confirmar Alteração <img src="../../imgs/ok_s.png" /></a><br />
				<a href="<?php echo 'album.php?db=' . $db . '&order=' . $order . '&id=' . $id . '&itemid=' . $itemid; ?>">Voltar</a><br /></div>
			<div class="clearfix"></div>
		</form>
	</body>
</html>