<?php namespace Hangman\Dictionary;

class Dictionary {
	public static function get_random_word_from_file($path) {
		if (file_exists($path)) {
			$dictionary = file($path);
		} else {
			return false;
		}

		$word = strtolower($dictionary[rand(0, sizeof($dictionary) - 1)]); 

		/* 
		check if word contains apostrophe,
		applies only to linux version of words file
		*/
		while (strpos($word, "'")) {
			$word = strtolower($dictionary[rand(0, sizeof($dictionary) - 1)]);
		}
		
		return $word;
	}
}