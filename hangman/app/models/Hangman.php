<?php 

namespace Hangman\Models;

use Hangman\Classes\Dictionary;
use Hangman\Classes\HTML;
use Hangman\Classes\Word;

class Hangman {
	public $word;
	public $word_cells;
	public $incorrect_guesses;
	public $letters_guessed;
	public $gallow_stage;

	public function __construct() {
		$this->word = Dictionary::get_random_word_from_file('/usr/share/dict/words');
		if (!$this->word)
			throw new \Exception('Error: unable to read words file');

		$this->word_cells = array_fill(0, strlen($this->word) - 1, ' ');
		$this->incorrect_guesses = 0;
		$this->letters_guessed = '';
		$this->gallow_stage = 0;
	}

	public function check_letter($letter) {
		// sanity check for letters only
		if (Word::only_letters($letter)) {
			// remove additional chars if present
			$letter = substr($letter, 0, 1);
		} else {
			return;
		}

		if ($this->new_guess($letter)) {
			$this->update_letters_guessed($letter);

			if ($this->incorrect_guess($letter)) {
				$this->gallow_stage++;
				$this->incorrect_guesses++;
			}
		}

	}

	private function new_guess($letter) {
		return strpos($this->letters_guessed, strtoupper($letter)) === false;
	}

	private function incorrect_guess($letter) {
		$incorrect = true;

		// check for occurences of letter in word and set in word cells if found
		$length = count($this->word_cells);
		for ($i = 0; $i < $length; $i++) {
			if (substr($this->word, $i, 1) == strtolower($letter)) {
				$this->update_word_cells($i, strtoupper($letter));
				$incorrect = false;
			}
		}
		return $incorrect;
	}

	public function word_complete() {
		return !in_array(' ', $this->word_cells);
	}

	public function get_word_cells_formatted() {
		return HTML::td($this->word_cells);
	}

	public function update_letters_guessed($guess) {
		$this->letters_guessed .= strtoupper($guess);
	}

	public function update_word_cells($index, $letter) {
		$this->word_cells[$index] = $letter;
	}
}