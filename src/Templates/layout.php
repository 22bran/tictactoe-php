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
            background-color: #FFF8E7;
            color: #555555;
        }
        input[type="checkbox"] {
            accent-color: #555555;
            filter: brightness(96%);
        }
        input {
            background-color: papayawhip;
            border: 1px dashed #555555;
            color: #555555;
        }
        input:disabled {
            background-color: lightgray;
            border: 1px dashed #555555;
            color: #555555;
        }
        .board {
            border-spacing: 0;
            border-collapse: collapse;
        }
        .layout {
            border-right: 1px #555555;
            border-top: 1px #555555;
            border-left: 1px #555555;
            border-style: dashed;
            border-bottom: 0px;
        }
        .layout td {
            text-align: -webkit-center;
            vertical-align: top;
        }
        .boardtd {
            border: 1px dashed #555555;
            width: 42px;
            min-width: 42px;
            height: 42px;
            text-align: center;
            background-color: papayawhip;
        }
        span.stone {
            font-size: xx-large;
        }
        span.lastStone {
            font-size: xx-large;
            color: #FF6B6B;
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