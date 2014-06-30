<?php

namespace Hangman\Tests;

use \Session as Session;

class IndexControllerTest extends \TestCase {
	public function test_new_word_received_after_new_game_clicked() {
		// begin game
		$this->get('/');
		$original_word = unserialize(Session::get('game'))->hangman->word;
		// simulate clicking the new game button
		$this->get('/');
		$new_word = unserialize(Session::get('game'))->hangman->word;

		$this->assertNotEquals($original_word, $new_word);
	}

	public function test_new_word_received_after_letters_filled_in_and_then_new_game_clicked() {
		// begin game
		$this->get('/');
		$game = unserialize(Session::get('game'));
		$original_word = $game->hangman->word;
		$letter_in_word = substr($game->hangman->word, 0, 1);
		// guess correct letter
		$this->get('/?letter=' . $letter_in_word);
		// simulate clicking the new game button
		$this->get('/');
		$game = unserialize(Session::get('game'));
		$new_word = $game->hangman->word;

		$this->assertNotEquals($original_word, $new_word);
	}

	public function test_statistics_persist_when_new_game_clicked() {
		// begin game
		$this->get('/');
		// guess random letter
		$this->get('/?letter=A');
		$game = unserialize(Session::get('game'));
		$game->record->games_won++;
		Session::put('game', serialize($game));
		// guess another random letter, game will now be complete
		$this->get('/?letter=E');
		// simulate clicking the new game button
		$this->get('/');
		$game = unserialize(Session::get('game'));

		$result = $game->record->games_won;

		$this->assertEquals(1, $result);
	}

	public function test_games_won_variable_increases_on_win() {
		// begin game
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
		$this->get('/?letter=' . $chars[0]);
		$game = unserialize(Session::get('game'));

		$result = $game->record->games_won;

		$this->assertEquals(1, $result);
	}

	public function test_success_message_displayed_on_game_win() {
		// begin game
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
		$response = $this->get('/?letter=' . $chars[0]);

		$this->assertContains('success', $response->getContent());
	}

	public function test_games_lost_variable_increases_on_game_loss() {
		// begin game
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
		$this->get('/?letter=' . $incorrect_letter);
		$game = unserialize(Session::get('game'));

		$result = $game->record->games_lost;

		$this->assertEquals(1, $result);
	}

	public function test_failure_message_displayed_on_game_loss() {
		// begin game
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
		$response = $this->get('/?letter=' . $incorrect_letter);

		$this->assertContains('failure', $response->getContent());	
	}

	public function test_incorrect_guesses_not_affected_by_page_refresh() {
		// begin game
		$this->get('/');
		$game = unserialize(Session::get('game'));
		$word = $game->hangman->word;
		$alphabet = str_split('abcdefghijklmnopqrstuvwxyz');

		$incorrect_letter = '';
		$length = count($alphabet);
		for ($i = 0; $i < $length; $i++) {
			// find a letter that doesn't exist in the word
			if (strpos(substr($alphabet[$i], $i, 1), $word) === false) {
				$incorrect_letter = $alphabet[$i];
				break;
			}
		}

		// make incorrect guess
		$this->get('/?letter=' . $incorrect_letter);
		$game = unserialize(Session::get('game'));
		$expected_result = $game->hangman->incorrect_guesses;
		// page refresh
		$this->get('/?letter=' . $incorrect_letter);
		$game = unserialize(Session::get('game'));
		$actual_result = $game->hangman->incorrect_guesses;

		$this->assertEquals($expected_result, $actual_result);
	}

	public function test_gallow_goes_to_next_stage_after_incorrect_guess() {
		// begin game
		$this->get('/');
		$game = unserialize(Session::get('game'));
		$initial_stage = $game->hangman->gallow_stage;
		$word = $game->hangman->word;
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
		$game = unserialize(Session::get('game'));
		$next_stage = $game->hangman->gallow_stage;

		$this->assertNotEquals($initial_stage, $next_stage);
	}

	public function test_gallow_maintains_stage_when_page_refreshed_with_incorrect_letter() {
		// begin game
		$this->get('/');
		$game = unserialize(Session::get('game'));
		$word = $game->hangman->word;
		$alphabet = str_split('abcdefghijklmnopqrstuvwxyz');
		
		$incorrect_letter = '';
		$length = count($alphabet);
		for ($i = 0; $i < $length; $i++) {
			// find a letter that doesn't exist in the word
			if (strpos(substr($alphabet[$i], $i, 1), $word) == false) {
				$incorrect_letter = $alphabet[$i];
				break;
			}
		}

		// make incorrect guess
		$this->get('/?letter=' . $incorrect_letter);
		$game = unserialize(Session::get('game'));
		$initial_stage = $game->hangman->gallow_stage;
		$this->get('/?letter=' . $incorrect_letter);
		$game = unserialize(Session::get('game'));
		$next_stage = $game->hangman->gallow_stage;

		$this->assertEquals($initial_stage, $next_stage);
	}

	public function test_gallow_stage_resets_after_game_loss() {
		// begin game
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
		$this->get('/?letter=' . $incorrect_letter);
		$game = unserialize(Session::get('game'));
		$initial_gallow = $game->hangman->gallow_stage;
		// start new game
		$this->get('/');
		$game = unserialize(Session::get('game'));
		$next_gallow = $game->hangman->gallow_stage;

		$this->assertNotEquals($initial_gallow, $next_gallow);
	}

	public function test_guessing_letter_with_no_session_redirects_to_index() {
		$response = $this->get('/?letter=A');
		$redirect = 'Redirecting to http://localhost';

		$this->assertContains($redirect, $response->getContent());
	}

	public function test_refreshing_page_after_win_maintains_games_won_variable() {
		// begin game
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
		$this->get('/?letter=' . $chars[0]);
		$game = unserialize(Session::get('game'));
		$initial_result = $game->record->games_won;
		// page refresh
		$this->get('/?letter=' . $chars[0]);
		$game = unserialize(Session::get('game'));
		$next_result = $game->record->games_won;

		$this->assertEquals($initial_result, $next_result);
	}

	public function test_refreshing_page_after_loss_maintains_games_lost_variable() {
		// begin game
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
		$this->get('/?letter=' . $incorrect_letter);
		$game = unserialize(Session::get('game'));
		$initial_result = $game->record->games_lost;
		// page refresh
		$this->get('/?letter=' . $incorrect_letter);
		$game = unserialize(Session::get('game'));
		$after_result = $game->record->games_lost;;

		$this->assertEquals($initial_result, $after_result);
	}
}