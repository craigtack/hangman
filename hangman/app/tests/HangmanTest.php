<?php

namespace Hangman\Tests;

use Hangman\Models\Hangman;

class HangmanTest extends \TestCase {
	public function test_check_letter_function_passing_more_than_one_char() {
		$hangman = new Hangman();
		$hangman->check_letter('abc');

		$letters_guessed = $hangman->letters_guessed;

		$this->assertEquals('A', $letters_guessed);
	}

	public function test_check_letter_function_passing_numbers() {
		$hangman = new Hangman();
		$hangman->check_letter('123');

		$letters_guessed = $hangman->letters_guessed;

		$this->assertEquals('', $letters_guessed);
	}
}