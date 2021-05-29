//Login Validation - Start
function Login(){
	var username = $('.field#username').val();
	var password = $('.field#password').val();
	var error = 0;
	var browser = true;
	
	if (navigator.appVersion.indexOf("MSIE") != -1 && parseFloat(navigator.appVersion.split("MSIE")[1]) <= 8)
	{
		error++;
		browser = false;
	}
	
	var regex = /^([0-9a-zA-Z\ \.\,\-\@\/\!]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+$/
	
	//Nome
	if (username.length == 0 || username.length > 30 || !regex.test(username))
		error++;

	//E-mail
	if (password.length == 0 || password.length > 30 || !regex.test(password))
		error++;
	
	if (error == 0)
	{
		$('.submit').attr({'disabled' : 'true'});
		$.post("inc/connect.php", { user: username, pass: password }, function(result)
		{
			if(result == 'connected')
			{
				$('#loginError').slideUp(500);
				window.location = 'index.php';
			}
			else
			{
				document.getElementById('loginError').innerHTML = "Login ou Senha incorreto";
				$('#loginError').slideDown(500);
			}
		});
		$('.submit').removeAttr('disabled');
	}
	else
	{
		if (!browser)
		{
			document.getElementById('loginError').innerHTML = "A atual versão do seu navegador de internet não suporta esta Área Administrativa. Atualize-o ou substitua-o baixando um dos navegadores listados abaixo.</br></br>" + '<a title=Google Chrome" href="https://www.google.com/chrome?hl=pt-BR">Google Chrome</a></br><a title="Mozilla Firefox" href="http://www.mozilla.org/pt-BR/firefox/new/">Mozilla Firefox</a></br><a title="Opera" href="http://www.opera.com/">Opera</a></br><a title="Safari" href="http://www.apple.com/br/safari/">Safari</a></br><a title="Internet Explorer 9" href="http://www.internetexplorer9.com.br/">Internet Explorer 9</a>';
		}
		else
		{
			if(error == 1)
				document.getElementById('loginError').innerHTML = "Algum campo não foi preenchido ou está incorreto";
			else document.getElementById('loginError').innerHTML = "Os campos não foram preenchidos";
		}
		$('#loginError').slideDown(500);
	}
}
//Login Validation - End