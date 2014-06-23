<?php

class IndexControllerTest extends TestCase {
	public function test_new_word_received_after_new_game_clicked() {
		// begin game
		$this->call('GET', '/');
		$original_word = unserialize(Session::get('game'))->get_hangman()->get_word();
		// simulate clicking the new game button
		$this->call('GET', '/');
		$new_word = unserialize(Session::get('game'))->get_hangman()->get_word();

		$this->assertNotEquals($original_word, $new_word);
	}

	public function test_new_word_received_after_letters_filled_in_and_then_new_game_clicked() {
		// begin game
		$this->call('GET', '/');
		$game = unserialize(Session::get('game'));
		$original_word = $game->get_hangman()->get_word();
		$letter_in_word = substr($game->get_hangman()->get_word(), 0, 1);
		// guess correct letter
		$this->call('GET', '/?letter=' . $letter_in_word);
		// simulate clicking the new game button
		$this->call('GET', '/');
		$game = unserialize(Session::get('game'));
		$new_word = $game->get_hangman()->get_word();

		$this->assertNotEquals($original_word, $new_word);
	}

	public function test_statistics_persist_when_new_game_clicked() {
		// begin game
		$this->call('GET', '/');
		// guess random letter
		$this->call('GET', '/?letter=A');
		$game = unserialize(Session::get('game'));
		$game->get_record()->increase_games_won();
		Session::put('game', serialize($game));
		// guess another random letter, game will now be complete
		$this->call('GET', '/?letter=E');
		// simulate clicking the new game button
		$this->call('GET', '/');
		$game = unserialize(Session::get('game'));

		$result = $game->get_record()->get_games_won();

		$this->assertEquals(1, $result);
	}

	public function test_games_won_variable_increases_on_win() {
		// begin game
		$this->call('GET', '/');
		$game = unserialize(Session::get('game'));
		$word = $game->get_hangman()->get_word();
		$chars = str_split($word);
		// fill all but one letter in word
		for ($i = 1; $i < count($chars); $i++) {
			$game->get_hangman()->set_word_cells($i, $chars[$i]);
		}
		Session::put('game', serialize($game));
		// guess final letter resulting in game win
		$this->call('GET', '/?letter=' . $chars[0]);
		$game = unserialize(Session::get('game'));

		$result = $game->get_record()->get_games_won();

		$this->assertEquals(1, $result);
	}

	public function test_success_message_displayed_on_game_win() {
		// begin game
		$this->call('GET', '/');
		$game = unserialize(Session::get('game'));
		$word = $game->get_hangman()->get_word();
		$chars = str_split($word);
		// fill all but one letter in word
		for ($i = 1; $i < count($chars); $i++) {
			$game->get_hangman()->set_word_cells($i, $chars[$i]);
		}
		Session::put('game', serialize($game));
		// guess final letter resulting in game win
		$response = $this->call('GET', '/?letter=' . $chars[0]);

		$this->assertContains('success', $response->getContent());
	}

	public function test_games_lost_variable_increases_on_game_loss() {
		// begin game
		$this->call('GET', '/');
		$game = unserialize(Session::get('game'));
		$word = $game->get_hangman()->get_word();
		$alphabet = str_split('abcdefghijklmnopqrstuvwxyz');

		$incorrect_guesses = 0;
		$incorrect_letter = ''; // final letter that will result in game loss
		for ($i = 0; $i < count($alphabet); $i++) {
			// find character not in word
			if (strpos($word, $alphabet[$i]) === false) {
				// don't set final incorrect letter
				if ($incorrect_guesses == 9) {
					$incorrect_letter = $alphabet[$i];
					break;
				}

				$game->get_hangman()->check_letter($alphabet[$i]);
				$incorrect_guesses++;
			}
		}

		Session::put('game', serialize($game));
		// guess final incorrect letter resulting in game loss
		$this->call('GET', '/?letter=' . $incorrect_letter);
		$game = unserialize(Session::get('game'));

		$result = $game->get_record()->get_games_lost();

		$this->assertEquals(1, $result);
	}

	public function test_failure_message_displayed_on_game_loss() {
		// begin game
		$this->call('GET', '/');
		$game = unserialize(Session::get('game'));
		$word = $game->get_hangman()->get_word();
		$alphabet = str_split('abcdefghijklmnopqrstuvwxyz');

		$incorrect_guesses = 0;
		$incorrect_letter = ''; // final letter that will result in game loss
		for ($i = 0; $i < count($alphabet); $i++) {
			// find character not in word
			if (strpos($word, $alphabet[$i]) === false) {
				// don't set final incorrect letter
				if ($incorrect_guesses == 9) {
					$incorrect_letter = $alphabet[$i];
					break;
				}

				$game->get_hangman()->check_letter($alphabet[$i]);
				$incorrect_guesses++;
			}
		}

		Session::put('game', serialize($game));
		// guess final incorrect letter resulting in game loss
		$response = $this->call('GET', '/?letter=' . $incorrect_letter);

		$this->assertContains('failure', $response->getContent());	
	}

	public function test_incorrect_guesses_not_affected_by_page_refresh() {
		// begin game
		$this->call('GET', '/');
		$game = unserialize(Session::get('game'));
		$word = $game->get_hangman()->get_word();
		$alphabet = str_split('abcdefghijklmnopqrstuvwxyz');

		$incorrect_letter = '';
		for ($i = 0; $i < count($alphabet); $i++) {
			// find a letter that doesn't exist in the word
			if (strpos(substr($alphabet[$i], $i, 1), $word) === false) {
				$incorrect_letter = $alphabet[$i];
				break;
			}
		}

		// make incorrect guess
		$this->call('GET', '/?letter=' . $incorrect_letter);
		$game = unserialize(Session::get('game'));
		$expected_result = $game->get_hangman()->get_incorrect_guesses();
		// page refresh
		$this->call('GET', '/?letter=' . $incorrect_letter);
		$game = unserialize(Session::get('game'));
		$actual_result = $game->get_hangman()->get_incorrect_guesses();

		$this->assertEquals($expected_result, $actual_result);
	}

	public function test_gallow_goes_to_next_stage_after_incorrect_guess() {
		// begin game
		$this->call('GET', '/');
		$game = unserialize(Session::get('game'));
		$initial_stage = $game->get_hangman()->get_gallow_stage();
		$word = $game->get_hangman()->get_word();
		$alphabet = str_split('abcdefghijklmnopqrstuvwxyz');

		$incorrect_letter = '';
		for ($i = 0; $i < count($alphabet); $i++) {
			// find a letter that doesn't exist in the word
			if (strpos($word, $alphabet[$i]) === false) {
				$incorrect_letter = $alphabet[$i];
				break;
			}
		}

		// make incorrect guess
		$this->call('GET', '/?letter=' . $incorrect_letter);
		$game = unserialize(Session::get('game'));
		$next_stage = $game->get_hangman()->get_gallow_stage();

		$this->assertNotEquals($initial_stage, $next_stage);
	}

	public function test_gallow_maintains_stage_when_page_refreshed_with_incorrect_letter() {
		// begin game
		$this->call('GET', '/');
		$game = unserialize(Session::get('game'));
		$word = $game->get_hangman()->get_word();
		$alphabet = str_split('abcdefghijklmnopqrstuvwxyz');
		
		$incorrect_letter = '';
		for ($i = 0; $i < count($alphabet); $i++) {
			// find a letter that doesn't exist in the word
			if (strpos(substr($alphabet[$i], $i, 1), $word) == false) {
				$incorrect_letter = $alphabet[$i];
				break;
			}
		}

		// make incorrect guess
		$this->call('GET', '/?letter=' . $incorrect_letter);
		$game = unserialize(Session::get('game'));
		$initial_stage = $game->get_hangman()->get_gallow_stage();
		$this->call('GET', '/?letter=' . $incorrect_letter);
		$game = unserialize(Session::get('game'));
		$next_stage = $game->get_hangman()->get_gallow_stage();

		$this->assertEquals($initial_stage, $next_stage);
	}

	public function test_gallow_stage_resets_after_game_loss() {
		// begin game
		$this->call('GET', '/');
		$game = unserialize(Session::get('game'));
		$word = $game->get_hangman()->get_word();
		$alphabet = str_split('abcdefghijklmnopqrstuvwxyz');

		$incorrect_guesses = 0;
		$incorrect_letter = ''; // final letter that will result in game loss
		for ($i = 0; $i < count($alphabet); $i++) {
			// find character not in word
			if (strpos($word, $alphabet[$i]) === false) {
				// don't set final incorrect letter
				if ($incorrect_guesses == 9) {
					$incorrect_letter = $alphabet[$i];
					break;
				}

				$game->get_hangman()->check_letter($alphabet[$i]);
				$incorrect_guesses++;
			}
		}

		Session::put('game', serialize($game));
		// guess final incorrect letter resulting in game loss
		$this->call('GET', '/?letter=' . $incorrect_letter);
		$game = unserialize(Session::get('game'));
		$initial_gallow = $game->get_hangman()->get_gallow_stage();
		// start new game
		$this->call('GET', '/');
		$game = unserialize(Session::get('game'));
		$next_gallow = $game->get_hangman()->get_gallow_stage();

		$this->assertNotEquals($initial_gallow, $next_gallow);
	}

	public function test_guessing_letter_with_no_session_redirects_to_index() {
		$response = $this->call('GET', '/?letter=A');
		$redirect = 'Redirecting to http://localhost';

		$this->assertContains($redirect, $response->getContent());
	}

	public function test_refreshing_page_after_win_maintains_games_won_variable() {
		// begin game
		$this->call('GET', '/');
		$game = unserialize(Session::get('game'));
		$word = $game->get_hangman()->get_word();
		$chars = str_split($word);
		// fill all but one letter in word
		for ($i = 1; $i < count($chars); $i++) {
			$game->get_hangman()->set_word_cells($i, $chars[$i]);
		}
		Session::put('game', serialize($game));
		// guess final letter resulting in game win
		$this->call('GET', '/?letter=' . $chars[0]);
		$game = unserialize(Session::get('game'));
		$initial_result = $game->get_record()->get_games_won();
		// page refresh
		$this->call('GET', '/?letter=' . $chars[0]);
		$game = unserialize(Session::get('game'));
		$next_result = $game->get_record()->get_games_won();

		$this->assertEquals($initial_result, $next_result);
	}

	public function test_refreshing_page_after_loss_maintains_games_lost_variable() {
		// begin game
		$this->call('GET', '/');
		$game = unserialize(Session::get('game'));
		$word = $game->get_hangman()->get_word();
		$alphabet = str_split('abcdefghijklmnopqrstuvwxyz');

		$incorrect_guesses = 0;
		$incorrect_letter = ''; // final letter that will result in game loss
		for ($i = 0; $i < count($alphabet); $i++) {
			// find character not in word
			if (strpos($word, $alphabet[$i]) === false) {
				// don't set final incorrect letter
				if ($incorrect_guesses == 9) {
					$incorrect_letter = $alphabet[$i];
					break;
				}

				$game->get_hangman()->check_letter($alphabet[$i]);
				$incorrect_guesses++;
			}
		}

		Session::put('game', serialize($game));
		// guess final incorrect letter resulting in game loss
		$this->call('GET', '/?letter=' . $incorrect_letter);
		$game = unserialize(Session::get('game'));
		$initial_result = $game->get_record()->get_games_lost();
		// page refresh
		$this->call('GET', '/?letter=' . $incorrect_letter);
		$game = unserialize(Session::get('game'));
		$after_result = $game->get_record()->get_games_lost();

		$this->assertEquals($initial_result, $after_result);
	}
}