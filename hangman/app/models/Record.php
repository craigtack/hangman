<?php 

namespace Hangman\Models;

class Record {
	public $games_won;
	public $games_lost;

	public function __construct() {
		$this->games_won = 0;
		$this->games_lost = 0;
	}
}