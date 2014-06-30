<?php 

namespace Hangman\Models;

class Game {
	public $hangman;
	public $record;
	public $result_message;

	public function __construct() {
		$this->hangman = new Hangman();
		$this->record = new Record();
		$this->result_message = '';
	}

	public function complete() {
		return $this->won() || $this->lost();
	}

	public function won() {
		return $this->hangman->incorrect_guesses < 10 && $this->hangman->word_complete();
	}	

	public function lost() {
		return $this->hangman->incorrect_guesses == 10;
	}

	public function reset() {
		// get new hangman object and clear result message
		$this->hangman = new Hangman();
		$this->result_message = '';
	}
}