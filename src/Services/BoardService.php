<?php

namespace TicTacToe\Services;

use TicTacToe\Dtos\DiagonalForFieldDto;
use TicTacToe\Entities\BaseField;

class BoardService
{
    /**
     * @param array<int,array<int,BaseField>> $board
     * @return array<int,BaseField>
     */
    public function getDiagonal(array $board, int $startRow, int $startColumn, bool $rightToLeft): array
    {
        $diagonal = [];
        while (isset($board[$startRow][$startColumn])) {
            $diagonal[] = $board[$startRow][$startColumn];
            $startRow++;
            $rightToLeft ? $startColumn-- : $startColumn++;
        }

        return $diagonal;
    }

    /**
     * @param array<int,array<int,BaseField>> $board
     */
    public function getDiagonalForField(array $board, int $row, int $column, bool $rightToLeft): DiagonalForFieldDto
    {
        $diagonal = [];
        $beforeRow = $row;
        $beforeColumn = $column;
        $afterRow = $row + 1;
        $afterColumn = $rightToLeft ? $column - 1 : $column + 1;
        while (isset($board[$beforeRow][$beforeColumn])) {
            $diagonal[] = $board[$beforeRow][$beforeColumn];
            $beforeRow--;
            $rightToLeft ? $beforeColumn++ : $beforeColumn--;
        }
        $diagonal = array_reverse($diagonal);
        $index = count($diagonal) - 1;
        while (isset($board[$afterRow][$afterColumn])) {
            $diagonal[] = $board[$afterRow][$afterColumn];
            $afterRow++;
            $rightToLeft ? $afterColumn-- : $afterColumn++;
        }

        return new DiagonalForFieldDto($diagonal, $index);
    }

}
