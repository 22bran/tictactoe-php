<?php

namespace TicTacToe\Entities;

use TicTacToe\Enums\FieldValue;

abstract class BaseField
{
    abstract public function isEmpty(): bool;
    abstract public function value(): ?FieldValue;
}
