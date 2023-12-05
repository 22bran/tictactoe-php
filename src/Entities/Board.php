<?php

namespace TicTacToe\Entities;

use TicTacToe\Enums\FieldValue;

class Board
{
    private const int START_INDEX = 0;

    /** @var array<int,array<int,BaseField>> */
    private array $board;

    public function __construct(
        public readonly int $rows,
        public readonly int $columns,
    ) {
        $this->board = array_fill(
            start_index: self::START_INDEX,
            count: $rows,
            value: array_fill(
                start_index: self::START_INDEX,
                count: $columns,
                value: new EmptyField()
            )
        );
    }

    public function setField(int $row, int $column, FieldValue $fieldValue): void
    {
        $this->board[$row][$column] = new Field($fieldValue);
    }

    public function setEmpty(int $row, int $column): void
    {
        $this->board[$row][$column] = new EmptyField();
    }

    public function getField(int $row, int $column): BaseField
    {
        return $this->board[$row][$column];
    }

    public function isEmpty(int $row, int $column): bool
    {
        return $this->board[$row][$column]->isEmpty();
    }

    public function exists(int $row, int $column): bool
    {
        return isset($this->board[$row][$column]);
    }

    /** @return array<int,array<int,BaseField>> */
    public function get(): array
    {
        return $this->board;
    }

    /** @param array<int,array<int,BaseField>> $board */
    public function set(array $board): void
    {
        $this->board = $board;
    }
}
