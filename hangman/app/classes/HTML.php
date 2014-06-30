<?php 

namespace Hangman\Classes;

class HTML {
	public static function td(array $items) {
		$markup = '';

		$length = count($items);
		for ($i = 0; $i < $length; $i++) {
			$markup .= "<td>{$items[$i]}</td>";
		}

		return $markup;
	}
}