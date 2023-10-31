<?php

namespace TicTacToe\Services;

use TicTacToe\Dtos\DiagonalForFieldDto;
use TicTacToe\Entities\BaseField;
use TicTacToe\Entities\Board;

class BoardService
{
    /**
     * @return array<int,BaseField>
     */
    public function getDiagonal(Board $board, int $startRow, int $startColumn, bool $rightToLeft): array
    {
        $diagonal = [];
        while ($board->exists($startRow, $startColumn)) {
            $diagonal[] = $board->getField($startRow, $startColumn);
            $startRow++;
            $rightToLeft ? $startColumn-- : $startColumn++;
        }

        return $diagonal;
    }

    public function getDiagonalForField(Board $board, int $row, int $column, bool $rightToLeft): DiagonalForFieldDto
    {
        $diagonal = [];
        $beforeRow = $row;
        $beforeColumn = $column;
        $afterRow = $row + 1;
        $afterColumn = $rightToLeft ? $column - 1 : $column + 1;
        while ($board->exists($beforeRow, $beforeColumn)) {
            $diagonal[] = $board->getField($beforeRow, $beforeColumn);
            $beforeRow--;
            $rightToLeft ? $beforeColumn++ : $beforeColumn--;
        }
        $diagonal = array_reverse($diagonal);
        $index = count($diagonal) - 1;
        while ($board->exists($afterRow, $afterColumn)) {
            $diagonal[] = $board->getField($afterRow, $afterColumn);
            $afterRow++;
            $rightToLeft ? $afterColumn-- : $afterColumn++;
        }

        return new DiagonalForFieldDto($diagonal, $index);
    }

}
