<?php
	$sql = "SELECT `name`, `email`, `login`, `id` FROM `{$users_db}` WHERE `login`='{$_SESSION['usere2']}'";
	$user = mysql_fetch_assoc(mysql_query($sql));
?>
<div id="registrationInc">
	<script type="text/javascript" charset="UTF-8" language="JavaScript">
		jQuery(document).ready(function(){
			jQuery('#submit.submit').click(function(){
				jQuery("#theForm.theForm").validationEngine();
			});
		});
		
		function finalized(wlocation)
		{
			$('form#theForm').find('div#formError').remove();
			$.post("inc/html/apply_user.php", $(".theForm#theForm").serialize(), function(result)
			{
				if (result == 'ok')
				{ window.location = wlocation + "adm/index.php"; }
				else if (result.substring(0,6) == 'exist-')
				{
					var name = result.substring(6);
					$('form#theForm').append('<div id="formError">O login \'' + $('input#2.stringField').val() + '\' já está em uso pelo usuário ' + name + '. Escolha outro login para acesso.</div>');
				}
				else alert(result);
			});
		}
	</script>
	<script src="inc/formFunc.js" type="text/javascript" charset="utf-8"></script>
	<form name="theForm" id="theForm" method="post" class="theForm" action="javascript: finalized('<?php echo $website_link; ?>');">
		<input type="hidden" name="id" value="<?php echo $user['id']; ?>" />
		<h1>Nome</h1>
		<input class="stringField validate[required,custom[onlyLetterNumber],maxSize[300]]" id="0" type="text" name="name" value="<?php echo $user['name']; ?>" />
		<h1>E-mail</h1>
		<input class="stringField validate[required,custom[email],maxSize[300]]" id="1" type="text" name="email" value="<?php echo $user['email']; ?>" />
		<h1>Usuário/Login</h1>
		<input class="stringField validate[required,custom[onlyLetterNumber],minSize[2],maxSize[20]]" id="2" type="text" name="login" value="<?php echo $user['login']; ?>" />
		<h1>Nova Senha</h1>
		<input class="stringField validate[custom[onlyLetterNumber],minSize[6],maxSize[20]]" type="password" id="password" name="password" value="" />
		<h1>Repetir Senha</h1>
		<input class="stringField validate[equals[password]]" type="password" id="3" value="" />
		<div id="bottom">
			<a title="Concluir"><input class="submit" id="submit" type="submit" value="" /></a>
			<a href="index.php" title="Cancelar"><img src="imgs/remove.png" /></a>
		</div>
	</form>
</div>
