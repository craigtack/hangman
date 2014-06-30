<?php

namespace Hangman\Tests;

use Hangman\Classes\HTML;

class HTMLTest extends \TestCase {
	public function test_td_function_passing_empty_array() {
		$result = HTML::td(array());

		$this->assertEquals('', $result);
	}

	public function test_td_function_passing_filled_array() {
		$expected_result = '<td>one</td><td>two</td><td>three</td>';
		$actual_result = HTML::td(array('one', 'two', 'three'));

		$this->assertEquals($expected_result, $actual_result);
	}
}