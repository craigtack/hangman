<?php namespace Hangman\Word;

class Word {
	public static function only_letters($string) {
		return preg_match('/^[a-zA-Z]+$/', $string) == true;
	}
}