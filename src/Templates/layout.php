<?php
if (!isset($this) || !isset($title)) {
    throw new Exception('Data not set properly');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?=$this->e($title)?></title>
    <style>
        body {
            font-family: 'Gill Sans', 'Gill Sans MT', Calibri, 'Trebuchet MS', sans-serif;
        }
        span {
            color: red;
        }
        .board {
            border-spacing: 0;
            border-collapse: collapse;
        }
        .layout {
            border-right: 2px black;
            border-top: 2px black;
            border-left: 2px black;
            border-style: solid;
            border-bottom: 0px;
        }
        .layout td {
            text-align: -webkit-center;
            vertical-align: top;
        }
        .boardtd {
            border: 1px solid black;
            width: 42px;
            min-width: 42px;
            height: 42px;
            text-align: center;
        }
        span.stone {
            font-size: xx-large;
        }
        span.lastStone {
            font-size: xx-large;
            color: black;
        }
        .move {
            visibility: hidden;
        }
        td:hover > .move {
            visibility: visible;
        }
        td:hover > .move a {
            text-decoration: none;
        }
    </style>
</head>
<body>
    <h1>TicTacToe</h1>
    <hr />
    <?=$this->section('content')?>
</body>
</html>