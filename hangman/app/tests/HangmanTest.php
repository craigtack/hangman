<?php

use Hangman\Hangman as Hangman;

class HangmanTest extends TestCase {
	public function test_check_letter_function_passing_more_than_one_char() {
		$hangman = new Hangman\Hangman();
		$hangman->check_letter('abc');

		$result = $hangman->get_letters_guessed();

		$this->assertEquals('A', $result);
	}

	public function test_check_letter_function_passing_numbers() {
		$hangman = new Hangman\Hangman();
		$hangman->check_letter('123');

		$result = $hangman->get_letters_guessed();

		$this->assertEquals('', $result);
	}
}