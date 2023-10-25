<?php

namespace TicTacToe\Dtos;

use TicTacToe\Entities\BaseField;

readonly class DiagonalForFieldDto
{
    /**
     * @param array<int,BaseField> $diagonal
     */
    public function __construct(
        public array $diagonal,
        public int $index
    ) {
    }
}
