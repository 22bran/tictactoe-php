<?php

namespace TicTacToe\Dtos;

readonly class PlayersScoreDto
{
    /**
     * @param array<int,array<int,int>> $player
     * @param array<int,array<int,int>> $nextPlayer
     */
    public function __construct(
        public array $player = [0 => []],
        public array $nextPlayer = [0 => []]
    ) {}
}
