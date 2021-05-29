function getCode(link) {
	var start = link.indexOf('v=');
	start++;
	if (start != 0)
		start++;
	var end = link.indexOf('&');
	var code = '';
	if (link.length > 10)
	{ if (start > 1) { code = end != -1 ? link.substr(start, end - start) : link.substr(start); } }
	return code;
}
function AddOverDiv(content) {
	$('body').append('<div id="OverflowBGDiv" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: black; opacity: 0.5; z-index: 3000;" onclick="RemoveOverDiv();"></div>');
	var left = (window.innerWidth / 2) - 305;
	var top = (window.innerHeight / 2) - 205;
	$('body').append('<div id="OverflowContentDiv" style="fixed: absolute; top: ' + top + 'px; left: ' + left + 'px; width: 600px; height: 400px; border: 10px solid #ccc; z-index: 1005; background-color: black;"></div>');
	if (content != 0)
		$('body').find('#OverflowContentDiv').append(content);
}
function RemoveOverDiv() {
	$('body').find('div#OverflowBGDiv').remove();
	$('body').find('div#OverflowContentDiv').remove();
}
function openYoutube(numb) {
	var value = getCode($('input.shortStringField#' + numb).val());
	if (value != '')
	{ AddOverDiv('<iframe width="600" height="400" src="https://www.youtube-nocookie.com/embed/' + value + '" frameborder="0" allowfullscreen></iframe>'); }
	else AddOverDiv(0);
}
function openMultipleYoutube(numb) {
	var value = getCode($('div#' + numb).find('input.shortStringField#' + numb).val());
	if (value != '')
	{ AddOverDiv('<iframe width="600" height="400" src="https://www.youtube-nocookie.com/embed/' + value + '" frameborder="0" allowfullscreen></iframe>'); }
	else AddOverDiv(0);
}