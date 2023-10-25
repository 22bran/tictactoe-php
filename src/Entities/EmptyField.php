<?php

namespace TicTacToe\Entities;

class EmptyField extends BaseField
{
    public function isEmpty(): true
    {
        return true;
    }

    public function value(): null
    {
        return null;
    }
}
