<?php namespace Hangman\Hangman;

use Hangman\Dictionary as Dictionary;
use Hangman\HTML as HTML;
use Hangman\Word as Word;

class Hangman {
	private $word;
	private $word_cells;
	private $incorrect_guesses;
	private $letters_guessed;
	private $gallow_stage;

	public function __construct() {
		$this->word = Dictionary\Dictionary::get_random_word_from_file('/usr/share/dict/words');
		if (!$this->word)
			throw new \Exception('Error: unable to read words file');

		$this->word_cells = array_fill(0, strlen($this->word) - 1, ' ');
		$this->incorrect_guesses = 0;
		$this->letters_guessed = '';
		$this->gallow_stage = 0;
	}

	public function check_letter($letter) {
		// sanity check for letters only
		if (Word\Word::only_letters($letter)) {
			// remove additional chars if present
			$letter = substr($letter, 0, 1);
		} else {
			return;
		}

		if ($this->new_guess($letter)) {
			$this->set_letters_guessed($letter);

			if ($this->incorrect_guess($letter)) {
				$this->increase_gallow_stage();
				$this->increase_incorrect_guesses();
			}
		}

	}

	private function new_guess($letter) {
		return strpos($this->letters_guessed, strtoupper($letter)) === false;
	}

	private function incorrect_guess($letter) {
		$incorrect = true;

		// check for occurences of letter in word and set in word cells if found
		for ($i = 0; $i < sizeof($this->word_cells); $i++) {
			if (substr($this->word, $i, 1) == strtolower($letter)) {
				$this->set_word_cells($i, strtoupper($letter));
				$incorrect = false;
			}
		}
		return $incorrect;
	}

	public function word_complete() {
		return !in_array(' ', $this->word_cells);
	}

	// getters and setters
	public function get_word() {
		return $this->word;
	}

	public function set_word($word) {
		$this->word = $word;
	}

	public function get_incorrect_guesses() {
		return $this->incorrect_guesses;
	}

	public function increase_incorrect_guesses() {
		$this->incorrect_guesses++;
	}

	public function get_letters_guessed() {
		return $this->letters_guessed;
	}

	public function set_letters_guessed($guess) {
		$this->letters_guessed .= strtoupper($guess);
	}

	public function get_word_cells() {
		return $this->word_cells;
	}

	public function get_word_cells_formatted() {
		return HTML\HTML::td($this->word_cells);
	}

	public function set_word_cells($index, $letter) {
		$this->word_cells[$index] = $letter;
	}

	public function get_gallow_stage() {
		return $this->gallow_stage;
	}

	public function increase_gallow_stage() {
		$this->gallow_stage++;
	}
}