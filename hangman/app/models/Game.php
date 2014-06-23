<?php namespace Hangman\Game;

use Hangman\Hangman as Hangman;
use Hangman\Record as Record;

class Game {
	private $hangman;
	private $record;
	private $result_message;

	public function __construct() {
		$this->hangman = new Hangman\Hangman();
		$this->record = new Record\Record();
		$this->result_message = '';
	}

	public function get_hangman() {
		return $this->hangman;
	}

	public function get_record() {
		return $this->record;
	}

	public function complete() {
		return $this->won() || $this->lost();
	}

	public function won() {
		return $this->hangman->get_incorrect_guesses() < 10 && $this->hangman->word_complete();
	}	

	public function lost() {
		return $this->hangman->get_incorrect_guesses() == 10;
	}

	public function reset() {
		// get new hangman object and clear result message
		$this->hangman = new Hangman\Hangman();
		$this->set_result_message('');
	}

	public function get_result_message() {
		return $this->result_message;
	}

	public function set_result_message($result_message) {
		$this->result_message = $result_message;
	}
}