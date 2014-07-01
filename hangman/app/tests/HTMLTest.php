<?php

namespace Hangman\Tests;

use Hangman\Classes\HTML;

class HTMLTest extends \TestCase {
	public function test_td_function_passing_empty_array() {
		$td = HTML::td(array());

		$this->assertEquals('', $td);
	}

	public function test_td_function_passing_filled_array() {
		$expected_html = '<td>one</td><td>two</td><td>three</td>';
		$actual_html = HTML::td(array('one', 'two', 'three'));

		$this->assertEquals($expected_html, $actual_html);
	}
}