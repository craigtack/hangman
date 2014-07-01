<?php

Route::group(array('namespace' => 'Hangman\Controllers'), function() {
	Route::get('/', array('as' => 'index', 'uses' => 'IndexController@get_index'));
});