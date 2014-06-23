<?php namespace Hangman\Record;

class Record {
	private $games_won;
	private $games_lost;

	public function __construct() {
		$this->games_won = 0;
		$this->games_lost = 0;
	}

	public function get_games_won() {
		return $this->games_won;
	}

	public function increase_games_won() {
		$this->games_won++;
	}

	public function get_games_lost() {
		return $this->games_lost;
	}

	public function increase_games_lost() {
		$this->games_lost++;
	}
}