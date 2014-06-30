@extends('layouts.master')

@section('head')
	<title>Hangman</title>
	<link rel="stylesheet" href="css/style.css">
	<script src="js/jquery-2.1.1.min.js"></script>
	<script src="js/disable.js"></script>
	<script src="js/handlers.js"></script>
@stop

@section('content')
	<header>
		<div class="banner">
			<h1>hangman</h1>
		</div>
		<div class="new_game">
			<a 	href="/" onclick="return newGameHandler();">New Game</a>
		</div>
	</header>
	<div class="game">
		<div class="gallow">
			<?= "<img src='../img/gallow_{$gallow_stage}.png' alt='gallow'>"; ?>
		</div>
		<aside class="statistics">
			<?php
				echo "<h3>Games won: <strong>{$games_won}</strong></h3>"; 
				echo "<h3>Games lost: &nbsp;<strong>{$games_lost}</strong></h3>";
			?>
			<div id="result_message">
				<?= $result_message ?>
			</div>
		</aside>
	</div>
	<div class="word">
		<table id="cells">
			<tr>
				<?= $word_cells; ?>
			</tr>
		</table>
	</div>
	<div id="letters">
		<ul>
			<li>
				<a href="/?letter=A" id="A">A</a>
			</li>
			<li>
				<a href="/?letter=B" id="B">B</a>
			</li>
			<li>
				<a href="/?letter=C" id="C">C</a>
			</li>
			<li>
				<a href="/?letter=D" id="D">D</a>
			</li>
			<li>
				<a href="/?letter=E" id="E">E</a>
			</li>
			<li>
				<a href="/?letter=F" id="F">F</a>
			</li>
			<li>
				<a href="/?letter=G" id="G">G</a>
			</li>
			<li>
				<a href="/?letter=H" id="H">H</a>
			</li>
			<li>
				<a href="/?letter=I" id="I">I</a>
			</li>
			<li>
				<a href="/?letter=J" id="J">J</a>
			</li>
			<li>
				<a href="/?letter=K" id="K">K</a>
			</li>
			<li>
				<a href="/?letter=L" id="L">L</a>
			</li>
			<li>
				<a href="/?letter=M" id="M">M</a>
			</li>
			<li>
				<a href="/?letter=N" id="N">N</a>
			</li>
			<li>
				<a href="/?letter=O" id="O">O</a>
			</li>
			<li>
				<a href="/?letter=P" id="P">P</a>
			</li>
			<li>
				<a href="/?letter=Q" id="Q">Q</a>
			</li>
			<li>
				<a href="/?letter=R" id="R">R</a>
			</li>
			<li>
				<a href="/?letter=S" id="S">S</a>
			</li>
			<li>
				<a href="/?letter=T" id="T">T</a>
			</li>
			<li>
				<a href="/?letter=U" id="U">U</a>
			</li>
			<li>
				<a href="/?letter=V" id="V">V</a>
			</li>
			<li>
				<a href="/?letter=W" id="W">W</a>
			</li>
			<li>
				<a href="/?letter=X" id="X">X</a>
			</li>
			<li>
				<a href="/?letter=Y" id="Y">Y</a>
			</li>
			<li>
				<a href="/?letter=Z" id="Z">Z</a>
			</li>
		</ul>
	</div>
	<p hidden id="letters_guessed"><?= $letters_guessed ?></p>
@stop