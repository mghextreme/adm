<?php
	switch($_GET['action'])
	{
		case 'edit':
			$text = 'editado';
			break;
		case 'add':
			$text = 'adicionado';
			break;
		case 'remove':
			$text = 'removido';
			break;
	}
	echo '<p><br />O registro foi ' . $text . ' com sucesso.';
?>
<br /><br />
<a href="<?php echo 'index.php?menu=' . $_GET['menu']; ?>"><img src="imgs/ok.png" /></a>