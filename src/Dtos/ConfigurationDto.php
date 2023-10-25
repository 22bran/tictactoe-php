<?php

namespace TicTacToe\Dtos;

readonly class ConfigurationDto
{
    public function __construct(
        public int $rows,
        public int $columns,
        public int $stones,
        public string $playerX,
        public string $playerO,
        public bool $playerXisComputer,
        public bool $playerOisComputer
    ) {}

    /**
     * @param array<string,string> $postData
     */
    public static function createFromPostData(array $postData): self
    {
        $rows = isset($postData['rows']) ? (int) $postData['rows'] : 0;
        $columns = isset($postData['columns']) ? (int) $postData['columns'] : 0;
        $stones = isset($postData['stones']) ? (int) $postData['stones'] : 0;
        $playerX = $postData['player_x_nick'] ?? 'COMPUTER X';
        $playerO = $postData['player_o_nick'] ?? 'COMPUTER O';
        $playerXisComputer = array_key_exists('player_x_is_computer', $postData);
        $playerOisComputer = array_key_exists('player_o_is_computer', $postData);

        return new self(
            $rows,
            $columns,
            $stones,
            $playerX,
            $playerO,
            $playerXisComputer,
            $playerOisComputer
        );
    }
}
