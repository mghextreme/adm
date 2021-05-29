//Logout Confirm - Start
function Logout(admlink) {
	$.post("inc/disconnect.php", function(result)
	{
		if(result == 'disconnected')
		{ window.location = admlink + 'login.php'; }
		else alert(result);
	});
}
//Logout Confirm - End