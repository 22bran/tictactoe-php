<?php

namespace TicTacToe\Helpers;

use TicTacToe\Entities\BaseField;

class ColumnsHelper
{
    /**
     * @param array<int,array<int,BaseField>> $board
     * @return array<int,array<int,BaseField>>
     */
    public static function getColumns(int $columnsCount, array $board): array
    {
        $columns = [];
        for ($i = 0; $i < $columnsCount; $i++) {
            $columns[] = array_column($board, $i);
        }
        return $columns;
    }
}
