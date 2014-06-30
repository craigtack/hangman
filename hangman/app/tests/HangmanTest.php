<?php

namespace Hangman\Tests;

use Hangman\Models\Hangman;

class HangmanTest extends \TestCase {
	public function test_check_letter_function_passing_more_than_one_char() {
		$hangman = new Hangman();
		$hangman->check_letter('abc');

		$result = $hangman->letters_guessed;

		$this->assertEquals('A', $result);
	}

	public function test_check_letter_function_passing_numbers() {
		$hangman = new Hangman();
		$hangman->check_letter('123');

		$result = $hangman->letters_guessed;

		$this->assertEquals('', $result);
	}
}