<?php

namespace TicTacToe\Dtos;

readonly class MovesRelevancyDto
{
    /**
     * @param array<int,array<int,PlayersScoreDto>> $movesRelevancy
     * @param array<int,array<int,string>> $movesRelevancyDetail
     */
    public function __construct(
        public array $movesRelevancy,
        public array $movesRelevancyDetail
    ) {
    }
}
