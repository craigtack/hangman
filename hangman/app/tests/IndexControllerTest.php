<?php

namespace Hangman\Tests;

use \Session as Session;

class IndexControllerTest extends \TestCase {
	/*
	VIEW
	*/
	public function test_view_has_gallow_stage() {
		$this->get('/');

		$this->assertViewHas('gallow_stage');
	}

	public function test_view_has_games_won() {
		$this->get('/');

		$this->assertViewHas('games_won');
	}

	public function test_view_has_games_lost() {
		$this->get('/');

		$this->assertViewHas('games_lost');
	}

	public function test_view_has_result_message() {
		$this->get('/');

		$this->assertViewHas('result_message');
	}

	public function test_view_has_word_cells() {
		$this->get('/');

		$this->assertViewHas('word_cells');
	}

	public function test_view_has_letters_guessed() {
		$this->get('/');

		$this->assertViewHas('letters_guessed');
	}


	/*
	SESSION
	*/
	public function test_session_has_game() {
		$this->get('/');

		$this->assertSessionHas('game');
	}


	/*
	FUNCTIONAL
	*/
	public function test_new_word_received_after_refresh() {
		$this->get('/');
		$original_word = unserialize(Session::get('game'))->hangman->word;

		// refresh
		$this->get('/');
		$new_word = unserialize(Session::get('game'))->hangman->word;

		$this->assertNotEquals($original_word, $new_word);
	}

	public function test_new_word_received_after_letters_filled_in_and_then_refresh() {
		$this->get('/');
		$original_word = unserialize(Session::get('game'))->hangman->word;
		$letter_in_word = substr($original_word, 0, 1);

		// guess correct letter
		$this->get('/?letter=' . $letter_in_word);

		// refresh
		$this->get('/');
		$new_word = unserialize(Session::get('game'))->hangman->word;

		$this->assertNotEquals($original_word, $new_word);
	}

	public function test_statistics_persist_after_refresh() {
		$this->get('/');

		// guess random letter
		$this->get('/?letter=A');

		$game = unserialize(Session::get('game'));
		$game->record->games_won++;
		Session::put('game', serialize($game));

		// guess another random letter, game will now be complete
		$this->get('/?letter=E');
		
		// refresh
		$this->get('/');
		$games_won = unserialize(Session::get('game'))->record->games_won;

		$this->assertEquals(1, $games_won);
	}

	public function test_games_lost_variable_increases_after_refresh() {
		$this->get('/');

		// refresh
		$this->get('/');

		$games_lost = unserialize(Session::get('game'))->record->games_lost;

		$this->assertEquals(1, $games_lost);
	}

	public function test_games_won_variable_increases_on_win() {
		$this->winGame();
		
		$games_won = unserialize(Session::get('game'))->record->games_won;

		$this->assertEquals(1, $games_won);
	}

	public function test_success_message_displayed_on_game_win() {
		$response = $this->winGame()['response'];

		$this->assertContains('success', $response->getContent());
	}

	public function test_games_lost_variable_persists_after_winning_game_and_beginning_new_game() {
		$this->winGame();

		// begin new game
		$this->get('/');

		$games_lost = unserialize(Session::get('game'))->record->games_lost;

		$this->assertEquals(0, $games_lost);
	}

	public function test_games_lost_variable_increases_on_game_loss() {
		$this->loseGame();

		$games_lost = unserialize(Session::get('game'))->record->games_lost;

		$this->assertEquals(1, $games_lost);
	}

	public function test_failure_message_displayed_on_game_loss() {
		$response = $this->loseGame();

		$this->assertContains('failure', $response->getContent());	
	}

	public function test_games_lost_variable_persists_after_losing_game_and_beginning_new_game() {
		$this->loseGame();

		// begin new game
		$this->get('/');

		$games_lost = unserialize(Session::get('game'))->record->games_lost;

		$this->assertEquals(1, $games_lost);
	}

	public function test_incorrect_guesses_not_affected_by_refresh() {
		$incorrect_letter = $this->guessIncorrectLetter();

		$original_incorrect_guesses = unserialize(Session::get('game'))->hangman->incorrect_guesses;

		// refresh
		$this->get('/?letter=' . $incorrect_letter);

		$new_incorrect_guesses = unserialize(Session::get('game'))->hangman->incorrect_guesses;

		$this->assertEquals($original_incorrect_guesses, $new_incorrect_guesses);
	}

	public function test_gallow_goes_to_next_stage_after_incorrect_guess() {
		$this->guessIncorrectLetter();

		$gallow_stage = unserialize(Session::get('game'))->hangman->gallow_stage;

		$this->assertEquals(1, $gallow_stage);
	}

	public function test_gallow_maintains_stage_when_page_refreshed_with_incorrect_letter() {
		$incorrect_letter = $this->guessIncorrectLetter();

		$initial_stage = unserialize(Session::get('game'))->hangman->gallow_stage;

		// refresh
		$this->get('/?letter=' . $incorrect_letter);

		$next_stage = unserialize(Session::get('game'))->hangman->gallow_stage;

		$this->assertEquals($initial_stage, $next_stage);
	}

	public function test_gallow_stage_resets_after_game_loss() {
		$this->loseGame();

		// begin new game
		$this->get('/');

		$gallow_stage = unserialize(Session::get('game'))->hangman->gallow_stage;

		$this->assertEquals(0, $gallow_stage);
	}

	public function test_guessing_letter_with_no_session_redirects_to_index() {
		$response = $this->get('/?letter=A');

		$this->assertRedirectedTo('/');
	}

	public function test_refreshing_page_after_win_maintains_games_won_variable() {
		$refresh_uri = $this->winGame()['refresh_uri'];

		$original_games_won = unserialize(Session::get('game'))->record->games_won;

		// refresh
		$this->get('/');

		$new_games_won = unserialize(Session::get('game'))->record->games_won;

		$this->assertEquals($original_games_won, $new_games_won);
	}

	public function test_refreshing_page_after_loss_maintains_games_lost_variable() {
		$incorrect_letter = $this->loseGame();

		$original_games_lost = unserialize(Session::get('game'))->record->games_lost;

		// refresh
		$this->get('/?letter=' . $incorrect_letter);

		$new_games_lost = unserialize(Session::get('game'))->record->games_lost;

		$this->assertEquals($original_games_lost, $new_games_lost);
	}


	/*
	helper funcs
	*/
	private function winGame() {
		$this->get('/');

		$game = unserialize(Session::get('game'));
		$word = $game->hangman->word;
		$chars = str_split($word);

		// fill all but one letter in word
		$length = count($chars);
		for ($i = 1; $i < $length; $i++) {
			$game->hangman->update_word_cells($i, $chars[$i]);
		}

		Session::put('game', serialize($game));

		// guess final letter resulting in game win
		return array('response' => $this->get('/?letter=' . $chars[0]),
						'refresh_uri' => '/?letter=' . $chars[0]);
	}

	private function loseGame() {
		$this->get('/');

		$game = unserialize(Session::get('game'));
		$word = $game->hangman->word;
		$alphabet = str_split('abcdefghijklmnopqrstuvwxyz');

		$incorrect_guesses = 0;
		$incorrect_letter = ''; // final letter that will result in game loss
		$length = count($alphabet);
		for ($i = 0; $i < $length; $i++) {
			// find character not in word
			if (strpos($word, $alphabet[$i]) === false) {
				// don't set final incorrect letter
				if ($incorrect_guesses == 9) {
					$incorrect_letter = $alphabet[$i];
					break;
				}

				$game->hangman->check_letter($alphabet[$i]);
				$incorrect_guesses++;
			}
		}

		Session::put('game', serialize($game));

		// guess final incorrect letter resulting in game loss
		return $this->get('/?letter=' . $incorrect_letter);
	}

	private function guessIncorrectLetter() {
		$this->get('/');

		$word = unserialize(Session::get('game'))->hangman->word;
		$alphabet = str_split('abcdefghijklmnopqrstuvwxyz');

		$incorrect_letter = '';
		$length = count($alphabet);
		for ($i = 0; $i < $length; $i++) {
			// find a letter that doesn't exist in the word
			if (strpos($word, $alphabet[$i]) === false) {
				$incorrect_letter = $alphabet[$i];
				break;
			}
		}

		// make incorrect guess
		$this->get('/?letter=' . $incorrect_letter);

		return $incorrect_letter;
	}
}