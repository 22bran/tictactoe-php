<?php

namespace TicTacToe\Services;

use TicTacToe\Enums\FieldValue;
use TicTacToe\Entities\BaseField;
use TicTacToe\Entities\Field;

class WinnerService
{
    private ChainService $chainService;
    private BoardService $boardService;

    public function __construct(

    ) {
        $this->chainService = new ChainService();
        $this->boardService = new BoardService();
    }

    /**
     * @param array<int,array<int,BaseField>> $lines
     */
    private function findWinnerInLines(array $lines, int $stonesCount): FieldValue|false
    {
        foreach($lines as $line) {
            $winner = $this->chainService->isWinner($stonesCount, ...$line);
            if ($winner !== false) {
                return $winner;
            }
        }
        return false;
    }

    /**
     * @param array<int,array<int,BaseField>> $board
     */
    private function findWinnerInDiagonal(int $rows, int $columns, int $stones, array $board, int $stonesCount, bool $rightToLeft): FieldValue|false
    {
        $columnStart = $rightToLeft ? $columns - 1 : 0;
        $columnEnd = $rightToLeft ? 0 : $columns - $stones + 1;

        for ($row = 0; $row < ($rows - $stones + 1); $row++) {
            for ($column = $columnStart; $rightToLeft ? $column >= $columnEnd : $column < $columnEnd; $rightToLeft ? $column-- : $column++) {
                $diagonalWinner = $this->chainService->isWinner($stonesCount, ...$this->boardService->getDiagonal($board, $row, $column, $rightToLeft));
                if ($diagonalWinner !== false) {
                    return $diagonalWinner;
                }
            }
        }
        return false;
    }

    /**
     * @param array<int,array<int,BaseField>> $board
     */
    public function winner(int $rows, int $columns, int $stones, array $board, int $stonesCount): FieldValue|false
    {
        if ($winner = $this->findWinnerInLines($board, $stonesCount)) {
            return $winner;
        }

        if ($winner = $this->findWinnerInLines($this->getColumns($columns, $board), $stonesCount)) {
            return $winner;
        }

        if ($winner = $this->findWinnerInDiagonal($rows, $columns, $stones, $board, $stonesCount, false)) {
            return $winner;
        }

        if ($winner = $this->findWinnerInDiagonal($rows, $columns, $stones, $board, $stonesCount, true)) {
            return $winner;
        }

        return false;
    }

    /**
     * @param array<int,array<int,BaseField>> $board
     * @return array<int,array<int,BaseField>>
     */
    private function fillEmpty(int $rows, int $columns, array $board, FieldValue $fieldValue): array
    {
        $newBoard = $board;
        for ($row = 0; $row < $rows; $row++) {
            for ($column = 0; $column < $columns; $column++) {
                if ($board[$row][$column]->isEmpty()) {
                    $newBoard[$row][$column] = new Field($fieldValue);
                }
            }
        }

        return $newBoard;
    }

    /**
     * @param array<int,array<int,BaseField>> $board
     */
    public function draw(int $rows, int $columns, int $stones, array $board, int $stonesCount, FieldValue $fieldValue): bool
    {
        $newBoard = $this->fillEmpty($rows, $columns, $board, $fieldValue);

        return $this->winner($rows, $columns, $stones, $newBoard, $stonesCount) === false;
    }

    /**
     * @param array<int,array<int,BaseField>> $board
     * @return array<int,array<int,BaseField>>
     */
    public function getColumns(int $columnsCount, array $board): array
    {
        $columns = [];
        for ($i = 0; $i < $columnsCount; $i++) {
            $columns[] = array_column($board, $i);
        }
        return $columns;
    }
}
