<?php
if (!isset($this)) {
    throw new Exception('Data not set properly');
}
$this->layout('layout', ['title' => 'Configuration']) ?>

<form method="post" action="/board">
    <h3>Configuration:</h3>
    <label>Rows
        <input type="number" name="rows" min="3" max="20" value="3">
    </label>
    <label>Columns
        <input type="number" name="columns" min="3" max="20" value="3">
    </label>
    <label>Stones
        <input type="number" name="stones" min="3" max="20" value="3">
    </label>
    <br><br>
    <label>Player X nick
        <input type="text" name="player_x_nick" value="Player 1 X">
        <input type="checkbox" name="player_x_is_computer" value="1" onchange="document.getElementsByName('player_x_nick')[0].disabled = !document.getElementsByName('player_x_nick')[0].disabled">Is computer
    </label>
    <br><br>
    <label>Player O nick
        <input type="text" name="player_o_nick" value="Player 2 O">
        <input type="checkbox" name="player_o_is_computer" value="1" onchange="document.getElementsByName('player_o_nick')[0].disabled = !document.getElementsByName('player_o_nick')[0].disabled">Is computer
    </label>
    <br><br>
    <input type="submit" value="Play">
</form>