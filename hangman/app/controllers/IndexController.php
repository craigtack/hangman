<?php

use Hangman\Game as Game;

class IndexController extends BaseController {
	public function get_index() {
		// query string check
		if (!Request::has('letter')) {
			return $this->new_game();
		} else {
			return $this->current_game();
		}
	}

	public function new_game() {
		// check for valid session
		if (!Session::has('game')) {
			// Hangman throws exception if WORDS_FILE can't be read
			try {
				$game = new Game\Game();
			} catch (Exception $e) {
				return View::make('error', array('error' => $e));
			}
		} else {
			// session exists, grab contents and reset
			$game = unserialize(Session::get('game'));
			$game->reset();
		}

		Session::put('game', serialize($game));

		return View::make('index', array('word_cells' => $game->get_hangman()->get_word_cells_formatted(), 
	  	   		  	 			    'letters_guessed' => $game->get_hangman()->get_letters_guessed(),
	        	   	  			       'gallow_stage' => $game->get_hangman()->get_gallow_stage(),
	    	     		   			      'games_won' => $game->get_record()->get_games_won(),
  		        		   	  		     'games_lost' => $game->get_record()->get_games_lost(),
					   	  		     'result_message' => $game->get_result_message()));
	}

	public function current_game() {
		$game = unserialize(Session::get('game'));

		/* 
		check if game does not exist but user had letter key in query string
	   	or if game was just completed and re-route
	   	*/
		if (!Session::has('game') || $game->complete()) {
			return Redirect::route('index');
		}

		$letter = Request::query('letter');
		$game->get_hangman()->check_letter($letter);

		if ($game->won()) {
			$game->set_result_message('<h4 id="success">You won!</h4>');
			$game->get_record()->increase_games_won();
		} else if ($game->lost()) {
			$game->set_result_message('<h4 id="failure">You lost!</h4><p>The word was <strong>' . 
										$game->get_hangman()->get_word() . '</strong></p>');
			$game->get_record()->increase_games_lost();
		}

		Session::put('game', serialize($game));

		return View::make('index', array('word_cells' => $game->get_hangman()->get_word_cells_formatted(),
		   	   			 		    'letters_guessed' => $game->get_hangman()->get_letters_guessed(),
	        			  		       'gallow_stage' => $game->get_hangman()->get_gallow_stage(),
	         	 		   			      'games_won' => $game->get_record()->get_games_won(),
  		        			  		     'games_lost' => $game->get_record()->get_games_lost(),
        				  		     'result_message' => $game->get_result_message()));
	}
}