function disableLetters() {
	var success = document.getElementById('success');
	var failure = document.getElementById('failure');
	var queryString = window.location.search;

	if (success != null || failure != null) {
		disableAll();
	} else if (queryString.indexOf('letter')) {
		disableUserGuessed();
	}
}

function disableAll() {
	var letters = document.getElementById('letters').getElementsByTagName('a');
	
	for (var i = 0; i < letters.length; i++) {
		hideLetter(letters[i]);
	}
}

function disableUserGuessed() {
	var lettersGuessed = document.getElementById('letters_guessed').innerHTML;
	// convert lettersGuessed string into array
	lettersGuessed = lettersGuessed.split('');

	for (var i = 0; i < lettersGuessed.length; i++) {
		hideLetter(document.getElementById(lettersGuessed[i]));
	}
}

function hideLetter(element) {
	element.className = "disabled";
	element.style.color = "white";
}