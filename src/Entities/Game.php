<?php

namespace TicTacToe\Entities;

use TicTacToe\Dtos\ConfigurationDto;
use TicTacToe\Dtos\MoveDto;
use TicTacToe\Enums\FieldValue;

class Game
{
    public Board $board;
    public Player $onTheMove;
    public Player|false $winner;
    public int $remainingMoves;
    public int $startTime;
    public ?int $endTime = null;
    /** @var array<int,MoveDto> */
    public array $moves;
    public bool $draw;

    public function __construct(
        public readonly int $rows,
        public readonly int $columns,
        public readonly int $stones,
        public readonly Player $playerX,
        public readonly Player $playerO
    ) {
        $this->startTime = time();
        $this->board = new Board($rows, $columns);
        $this->winner = false;
        $this->onTheMove = $playerX;
        $this->remainingMoves = $rows * $columns;
        $this->moves = [];
        $this->draw = false;
    }

    public function getPlayTime(): int
    {
        return $this->endTime - $this->startTime;
    }

    public function switchPlayer(): void
    {
        if ($this->onTheMove === $this->playerX) {
            $this->onTheMove = $this->playerO;
        } else {
            $this->onTheMove = $this->playerX;
        }
    }

    public function draw(): bool
    {
        return $this->draw;
    }

    public function remainingMoves(): int
    {
        return $this->remainingMoves;
    }

    public function started(): bool
    {
        return count($this->moves) > 0;
    }

    public static function createFromConfiguration(ConfigurationDto $configurationDto): self
    {
        return new self(
            $configurationDto->rows,
            $configurationDto->columns,
            $configurationDto->stones,
            new Player($configurationDto->playerX, FieldValue::X, $configurationDto->playerXisComputer),
            new Player($configurationDto->playerO, FieldValue::O, $configurationDto->playerOisComputer)
        );
    }

    public static function createFromGame(Game $game): self
    {
        return new self(
            $game->rows,
            $game->columns,
            $game->stones,
            $game->playerX,
            $game->playerO,
        );
    }
}
