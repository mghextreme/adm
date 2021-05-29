<script type="text/javascript" charset="UTF-8" language="JavaScript">
	jQuery(document).ready(function(){
		jQuery('#submit.submit').click(function(){
			jQuery("#theForm.theForm").validationEngine();
		});
	});
	
	function ActionDone() {
		$('body').append('<img id="EditCheck" src="imgs/check.png" height="40" style="position: fixed; bottom: 10px; right: 10px; opacity: 1;" />');
		setTimeout("$('body img#EditCheck').animate({opacity: 0}, 1500, function() { $('body img#EditCheck').remove(); });", 500);
	}
	
	function SubmitForm() {
		$.post('inc/manager/apply_add_table.php', $('#theForm').serialize(), function(result){
			if (result == 'ok')
			{ window.location = 'index.php?menu=manager&submenu=edit&link=' + $('#theForm').find('#tablesql').val(); }
			else if (result == 'exist')
			{
				$('div#bottom div#tableAddFail').slideDown(500);
				setTimeout("$('div#bottom div#tableAddFail').slideDown(500)", 2000);
			}
			else alert(result);
		});
	}
</script>
<form method="post" action="javascript: SubmitForm();" id="theForm" class="theForm">
	<h1>Título da Tabela</h1>
	<input type="text" name="tablename" value="" id="tablename" class="stringField validate[required,minSize[3],custom[onlyLetterNumber]]" />
	<h1>Nome SQL</h1>
	<input type="text" name="tablesql" value="" id="tablesql" class="stringField validate[required,minSize[3],custom[onlyLetterSp]]" />
	<p id="sqlexplanation">* Não deve conter Caracteres especiais, somente letras minúsculas. Este nome será usado para identificação no Banco de Dados.</p>
	<h1>Uso exclusivo de Administrador</h1>
	<input type="checkbox" name="tableadmin" value="" id="tableadmin" />
	<h1>Tabela Simples (Conteúdo único)</h1>
	<input type="checkbox" name="singletable" value="" id="singletable" />
	<div id="bottom">
		<input title="Adicionar Tabela" type="submit" id="submit" class="submit" value="" />
		<a href="index.php?menu=manager" title="Cancelar"><img src="imgs/remove.png" /></a>
		<div id="tableAddFail" style="display: none; float: right; width: 300px; color: white; background-color: #9e1d29; padding: 8px;">Esta tabela já existe, insira um novo Nome SQL e tente novamente.</div>
	</div>
</form>