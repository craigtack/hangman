<?php namespace Hangman\HTML;

class HTML {
	public static function td(array $items) {
		$markup = '';

		for ($i = 0; $i < sizeof($items); $i++) {
			$markup .= "<td>{$items[$i]}</td>";
		}

		return $markup;
	}
}