<?php

use Hangman\HTML as Hangman;

class HTMLTest extends TestCase {
	public function test_td_function_passing_empty_array() {
		$result = Hangman\HTML::td(array());

		$this->assertEquals('', $result);
	}

	public function test_td_function_passing_filled_array() {
		$test_array = array('one', 'two', 'three');

		$expected_result = '<td>one</td><td>two</td><td>three</td>';
		$actual_result = Hangman\HTML::td($test_array);

		$this->assertEquals($expected_result, $actual_result);
	}
}