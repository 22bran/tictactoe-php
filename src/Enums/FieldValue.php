<?php

namespace TicTacToe\Enums;

enum FieldValue
{
    case X;
    case O;

    public function toString(): string
    {
        return match($this) {
            self::X => 'X',
            self::O => 'O',
        };
    }
}
