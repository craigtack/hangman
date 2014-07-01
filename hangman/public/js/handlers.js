// keyboard input
$(document).keypress(function(e) {
	var code = e.keyCode || e.which;
	if (code > 64 && code < 91 || code > 96 && code < 123) {
		var letter = String.fromCharCode(code).toUpperCase();
		document.getElementById(letter).click();
	}
});

// new game button
function newGameHandler() {
		var success = document.getElementById('success');
		var failure = document.getElementById('failure');

		if (success == null && failure == null) {
			if (!confirm('Are you want to start a new game? Doing so will count as a loss')) {
				return false;
			}
		}

	return true;
}