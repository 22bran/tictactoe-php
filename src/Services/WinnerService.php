<?php

namespace TicTacToe\Services;

use TicTacToe\Enums\FieldValue;
use TicTacToe\Entities\BaseField;
use TicTacToe\Entities\Field;
use TicTacToe\Entities\Game;
use TicTacToe\Helpers\ColumnsHelper;

class WinnerService
{
    public function __construct(
        private readonly ChainService $chainService,
        private readonly BoardService $boardService
    ) {}

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
    private function findWinnerInDiagonal(Game $game, array $board, bool $rightToLeft): FieldValue|false
    {
        $columnStart = $rightToLeft ? $game->columns - 1 : 0;
        $columnEnd = $rightToLeft ? 0 : $game->columns - $game->stones + 1;

        for ($row = 0; $row < ($game->rows - $game->stones + 1); $row++) {
            for ($column = $columnStart; $rightToLeft ? $column >= $columnEnd : $column < $columnEnd; $rightToLeft ? $column-- : $column++) {
                $diagonalWinner = $this->chainService->isWinner($game->stones, ...$this->boardService->getDiagonal($board, $row, $column, $rightToLeft));
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
    public function winner(Game $game, array $board): FieldValue|false
    {
        if ($winner = $this->findWinnerInLines($board, $game->stones)) {
            return $winner;
        }

        if ($winner = $this->findWinnerInLines(ColumnsHelper::getColumns($game->columns, $board), $game->stones)) {
            return $winner;
        }

        if ($winner = $this->findWinnerInDiagonal($game, $board, false)) {
            return $winner;
        }

        if ($winner = $this->findWinnerInDiagonal($game, $board, true)) {
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
    public function draw(Game $game, array $board, FieldValue $fieldValue): bool
    {
        $newBoard = $this->fillEmpty($game->rows, $game->columns, $board, $fieldValue);

        return $this->winner($game, $newBoard) === false;
    }
}
