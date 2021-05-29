function finalized(page, wlocation)
{
	$.post("inc/registration/apply_edit.php", ($(".theForm#theForm").serialize()+"&menu="+page), function(result)
	{
		if(result == 'ok')
		{ window.location = wlocation + "adm/index.php?menu=" + page; }
		else
		{ alert(result); }
	});
}

function singlefinalized(page, wlocation)
{
	$.post("inc/registration/apply_single_edit.php", ($(".theForm#theForm").serialize()+"&menu="+page), function(result)
	{
		if(result == 'ok')
		{ window.location = wlocation + "adm/index.php"; }
		else
		{ alert(result); }
	});
}

function addfinalized(page, wlocation)
{
	$.post("inc/registration/apply_add.php", ($(".theForm#theForm").serialize()+"&menu="+page), function(result)
	{
		if(result == 'ok')
		{ window.location = wlocation + "adm/index.php?menu=" + page; }
		else
		{ alert(result); }
	});
}

function remove(menu, itemid, wlocation)
{
	$.post("inc/registration/apply_remove.php", { page: menu, id: itemid }, function(result)
	{
		if(result == 'ok')
		{ window.location = wlocation + "adm/index.php?menu=" + menu; }
		else
		{ alert(result); }
	});
}
