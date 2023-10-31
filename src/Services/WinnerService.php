<?php

namespace TicTacToe\Services;

use TicTacToe\Enums\FieldValue;
use TicTacToe\Entities\Board;
use TicTacToe\Entities\Game;
use TicTacToe\Helpers\ColumnsHelper;
use TicTacToe\Entities\BaseField;

class WinnerService
{
    public function __construct(
        private readonly ChainService $chainService,
        private readonly BoardService $boardService
    ) {}

    /**
     * @param array<int,array<int,BaseField>> $board
     */
    private function findWinnerInLines(array $board, int $stonesCount): FieldValue|false
    {
        foreach($board as $line) {
            $winner = $this->chainService->isWinner($stonesCount, ...$line);
            if ($winner !== false) {
                return $winner;
            }
        }
        return false;
    }

    private function findWinnerInDiagonal(Game $game, Board $board, bool $rightToLeft): FieldValue|false
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

    public function winner(Game $game, Board $board): FieldValue|false
    {
        if ($winner = $this->findWinnerInLines($board->get(), $game->stones)) {
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

    private function fillEmpty(int $rows, int $columns, Board $board, FieldValue $fieldValue): Board
    {
        $newBoard = clone $board;
        for ($row = 0; $row < $rows; $row++) {
            for ($column = 0; $column < $columns; $column++) {
                if ($board->isEmpty($row, $column)) {
                    $newBoard->setField($row, $column, $fieldValue);
                }
            }
        }

        return $newBoard;
    }

    public function draw(Game $game, Board $board, FieldValue $fieldValue): bool
    {
        $newBoard = $this->fillEmpty($game->rows, $game->columns, $board, $fieldValue);

        return $this->winner($game, $newBoard) === false;
    }
}
