<?php

namespace TicTacToe\Dtos;

readonly class ScoreDto
{
    public function __construct(
        public int $stonesCount = 0,
        public int $score = 0
    ) {
    }
}
