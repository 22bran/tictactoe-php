<?php

namespace TicTacToe\Dtos;

readonly class MoveDto
{
    public function __construct(
        public int $row,
        public int $column
    ) {}

    /**
     * @param array<string,string> $getData
     */
    public static function createFromGetData(array $getData): self
    {
        $row = isset($getData['row']) ? (int) $getData['row'] : 0;
        $column = isset($getData['column']) ? (int) $getData['column'] : 0;

        return new self($row, $column);
    }
}
