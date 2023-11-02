<?php

namespace TicTacToe\Dtos;

readonly class AllDirectionsScoreDto
{
    public function __construct(
        public ScoreDto $horizontal,
        public ScoreDto $vertical,
        public ScoreDto $diagonalLeft,
        public ScoreDto $diagonalRight,
        public bool $winningMove
    ) {}

    /**
     * @return array<int,ScoreDto>
     */
    public function toArray(): array
    {
        return [$this->horizontal, $this->vertical, $this->diagonalLeft, $this->diagonalRight];
    }
}
