<?php

namespace TicTacToe\Entities;

use TicTacToe\Enums\FieldValue;

class Field extends BaseField
{
    public function __construct(
        public readonly FieldValue $value
    ) {
    }

    public function isEmpty(): false
    {
        return false;
    }

    public function value(): FieldValue
    {
        return $this->value;
    }
}
