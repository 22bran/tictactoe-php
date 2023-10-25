<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use TicTacToe\Dtos\ConfigurationDto;
use TicTacToe\Services\GameService;
use TicTacToe\Enums\FieldValue;
use TicTacToe\Entities\EmptyField;
use TicTacToe\Entities\Field;

final class GameServiceTest extends TestCase
{
    private GameService $gameService;
    private GameService $gameService8x8x5;

    protected function setUp(): void
    {
        $this->gameService = new GameService();
        $this->gameService->start(new ConfigurationDto(
            rows: 3,
            columns: 3,
            stones: 3,
            playerX: 'player X',
            playerO: 'player O',
            playerXisComputer: false,
            playerOisComputer: true
        ));
        $this->gameService8x8x5 = new GameService();
        $this->gameService8x8x5->start(new ConfigurationDto(
            rows: 8,
            columns: 8,
            stones: 5,
            playerX: 'player X',
            playerO: 'player O',
            playerXisComputer: false,
            playerOisComputer: true
        ));
    }

    public function testVariousBestMovesFor8x8x5(): void
    {
        $expected = [[
            'score' => 308,
            'scoreDetail' => "X\tH:3 (1)\tV:0 (0)\tL:0 (0)\tR:18 (4)\nO\tH:3 (1)\tV:10 (3)\tL:0 (0)\tR:0 (0)\nTotal score: 308",
            'row' => 1,
            'column' => 5,
        ]];

        $X = new Field(FieldValue::X);
        $O = new Field(FieldValue::O);
        $_ = new EmptyField();
        $board = [
            [
                $_, $_, $_, $_, $_, $_, $_, $_
            ],
            [
                $_, $_, $_, $_, $_, $_, $_, $_
            ],
            [
                $O, $_, $X, $X, $X, $O, $_, $_
            ],
            [
                $_, $_, $_, $X, $_, $_, $_, $_
            ],
            [
                $_, $_, $X, $O, $_, $_, $_, $_
            ],
            [
                $_, $_, $O, $X, $_, $O, $_, $_
            ],
            [
                $_, $_, $_, $O, $_, $_, $_, $_
            ],
            [
                $_, $_, $_, $_, $_, $_, $_, $_
            ],
        ];

        $allRelevantMoves = $this->gameService8x8x5->getAllRelevantMoves(
            board: $board,
            stoneType: FieldValue::X
        );
        $scores = [];
        foreach($allRelevantMoves as $relevantMove) {
            if($relevantMove->score !== null) {
                $scores[] = [
                    'score' => $relevantMove->score,
                    'scoreDetail' => $relevantMove->move->scoreDetail,
                    'row' => $relevantMove->move->row,
                    'column' => $relevantMove->move->column,
                ];
            }
        }
        $this->assertEquals($expected, $scores);
    }

    public function testSecondMoveScoreFor3x3x3(): void
    {
        $expected = [[
            'score' => 32,
            'row' => 1,
            'column' => 1,
        ]];

        $this->assertEquals($expected, $this->move(0, 0, FieldValue::X));
        $this->assertEquals($expected, $this->move(0, 1, FieldValue::X));
        $this->assertEquals($expected, $this->move(0, 2, FieldValue::X));

        $this->assertEquals($expected, $this->move(1, 0, FieldValue::X));
        $this->assertEquals($expected, $this->move(1, 2, FieldValue::X));
        
        $this->assertEquals($expected, $this->move(2, 0, FieldValue::X));
        $this->assertEquals($expected, $this->move(2, 1, FieldValue::X));
        $this->assertEquals($expected, $this->move(2, 2, FieldValue::X));

        $expected = [[
            'score' => 32,
            'row' => 0,
            'column' => 0,
        ],[
            'score' => 32,
            'row' => 0,
            'column' => 2,
        ],[
            'score' => 32,
            'row' => 2,
            'column' => 0,
        ],[
            'score' => 32,
            'row' => 2,
            'column' => 2,
        ]];

        $this->assertEquals($expected, $this->move(1, 1, FieldValue::X));
    }

    /**
     * @return array<int,array<string,int>>
     */
    private function move(int $row, int $column, FieldValue $stoneType): array {
        $board = array_fill(0, 3, array_fill(0, 3, new EmptyField()));
        $board[$row][$column] = new Field($stoneType);
        $allRelevantMoves = $this->gameService->getAllRelevantMoves(
            board: $board, 
            stoneType: $stoneType === FieldValue::O ? FieldValue::X : FieldValue::O
        );
        $scores = [];
        foreach($allRelevantMoves as $relevantMove) {
            if($relevantMove->score !== null) {
                $scores[] = [
                    'score' => $relevantMove->score,
                    'row' => $relevantMove->move->row,
                    'column' => $relevantMove->move->column,
                ];
            }
        }
        return $scores;
    }
}