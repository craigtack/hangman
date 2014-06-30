<?php 

namespace Hangman\Controllers;

use Hangman\Models\Game as Game;

class IndexController extends \BaseController {
	public function get_index() {
		// query string check
		if (!\Request::has('letter')) {
			return $this->new_game();
		} else {
			return $this->current_game();
		}
	}

	public function new_game() {
		// check for valid session
		if (!\Session::has('game')) {
			// Hangman throws exception if WORDS_FILE can't be read
			try {
				$game = new Game();
			} catch (\Exception $e) {
				return \View::make('error', array('error' => $e));
			}
		} else {
			// session exists, grab contents and reset
			$game = unserialize(\Session::get('game'));
			// check for forfeit, otherwise reset
			if (!$game->complete()) {
				$game->record->games_lost++;
			}
			$game->reset();
		}

		\Session::put('game', serialize($game));

		return \View::make('index', array('word_cells' => $game->hangman->get_word_cells_formatted(), 
	  	   		  	 			    'letters_guessed' => $game->hangman->letters_guessed,
	        	   	  			       'gallow_stage' => $game->hangman->gallow_stage,
	    	     		   			      'games_won' => $game->record->games_won,
  		        		   	  		     'games_lost' => $game->record->games_lost,
					   	  		     'result_message' => $game->result_message));
	}

	public function current_game() {
		$game = unserialize(\Session::get('game'));

		/* 
		check if game does not exist but user had letter key in query string
	   	or if game was just completed and re-route
	   	*/
		if (!\Session::has('game') || $game->complete()) {
			return \Redirect::route('index');
		}

		$letter = \Request::query('letter');
		$game->hangman->check_letter($letter);

		if ($game->won()) {
			$game->result_message = '<h4 id="success">You won!</h4>';
			$game->record->games_won++;
		} else if ($game->lost()) {
			$game->result_message = '<h4 id="failure">You lost!</h4><p>The word was <strong>' . 
										$game->hangman->word . '</strong></p>';
			$game->record->games_lost++;
		}

		\Session::put('game', serialize($game));

		return \View::make('index', array('word_cells' => $game->hangman->get_word_cells_formatted(),
		   	   			 		    'letters_guessed' => $game->hangman->letters_guessed,
	        			  		       'gallow_stage' => $game->hangman->gallow_stage,
	         	 		   			      'games_won' => $game->record->games_won,
  		        			  		     'games_lost' => $game->record->games_lost,
        				  		     'result_message' => $game->result_message));
	}
}