<?php

namespace TicTacToe\Entities;

use TicTacToe\Enums\FieldValue;

readonly class Player
{
    public function __construct(
        public string $name,
        public FieldValue $stoneType,
        public bool $isComputer = false
    ) {}
}
