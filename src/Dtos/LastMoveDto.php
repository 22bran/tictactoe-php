<?php

namespace TicTacToe\Dtos;

readonly class LastMoveDto
{
    public function __construct(
        public int $row,
        public int $column,
        public string $scoreDetail,
    ) {
    }
}
