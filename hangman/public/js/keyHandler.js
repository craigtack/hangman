$(document).keypress(function(e) {
	var code = e.keyCode || e.which;
	if (code > 64 && code < 91 || code > 96 && code < 123) {
		var letter = String.fromCharCode(code).toUpperCase();
		document.getElementById(letter).click();
	}
});