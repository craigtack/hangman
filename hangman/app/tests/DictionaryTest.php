<?php

namespace Hangman\Tests;

use Hangman\Classes\Dictionary;

class DictionaryTest extends \TestCase {
	public function test_get_random_word_from_file_function_with_good_path() {
		$word = Dictionary::get_random_word_from_file('/usr/share/dict/words');

		$this->assertTrue($word == true);
	}

	public function test_get_random_word_from_file_function_with_bad_path() {
		$word = Dictionary::get_random_word_from_file('/badfilepath');

		$this->assertFalse($word);
	}
}