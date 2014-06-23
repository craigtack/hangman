@extends('layouts.master')

@section('head')
	<title>Hangman - Error</title>
@stop

@section('content')
	<p>
		<?= $error ?>
	</p>
@stop