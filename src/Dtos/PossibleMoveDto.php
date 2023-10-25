<?php

namespace TicTacToe\Dtos;

use TicTacToe\Entities\BaseField;
use TicTacToe\Enums\FieldValue;

class PossibleMoveDto
{
    /**
     * @param array<int,array<int,BaseField>> $board
     * @param array<int,PossibleMoveDto> $children
     */
    public function __construct(
        public readonly array $board,
        public readonly bool $maximizer,
        public readonly PlayersScoreDto $scoreObject,
        public readonly int $score,
        public readonly FieldValue $stoneType,
        public readonly string $index,
        public readonly LastMoveDto $move,
        public array $children = [],
    ) {}
}
