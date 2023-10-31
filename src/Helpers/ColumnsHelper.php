<?php

namespace TicTacToe\Helpers;

use TicTacToe\Entities\BaseField;
use TicTacToe\Entities\Board;

class ColumnsHelper
{
    /**
     * @return array<int,array<int,BaseField>>
     */
    public static function getColumns(int $columnsCount, Board $board): array
    {
        $columns = [];
        for ($i = 0; $i < $columnsCount; $i++) {
            $columns[] = array_column($board->get(), $i);
        }
        return $columns;
    }
}
