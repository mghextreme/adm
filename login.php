<?php
	session_start();
	if (isset($_SESSION['usere2']))
	{
		echo '<META http-equiv="refresh" content="0;URL=index.php">';
		die;
	}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<link rel="shortcut icon" href="imgs/icon.ico">
		<title>Área administrativa | E2</title>
		<link href="css/style.css" rel="stylesheet" type="text/css" />
		<script type="text/javascript" src="js/jquery-1.5.1.min.js"></script>
		<script type="text/javascript" src="js/login.js"></script>
	</head>
	<body onLoad="document.formLogin.login.focus();">
		<div id="loginTop"><img src="imgs/logo_white.png" /></div>
		<img id="shadow" src="imgs/top_shadow.png" />
		<form id="login" method="post" name="formLogin" action="javascript: Login()">
			LOGIN<br>
			<input type="text" name="login" class="field required" id="username" size="30" />
			<br />
			SENHA<br>
			<input type="password" name="password" class="field required" id="password" size="30" />
			<br />
			<input type="submit" id="submit" class="submit" value="" />
		</form>
		<div id="loginError">
			Mensagem de Erro
		</div>
	</body>
</html>