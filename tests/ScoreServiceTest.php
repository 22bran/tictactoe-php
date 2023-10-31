<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use TicTacToe\Dtos\ConfigurationDto;
use TicTacToe\Entities\Board;
use TicTacToe\Enums\FieldValue;
use TicTacToe\Entities\EmptyField;
use TicTacToe\Entities\Field;
use TicTacToe\Services\ScoreService;
use TicTacToe\Entities\Game;
use TicTacToe\Services\BoardService;
use TicTacToe\Services\ChainService;

final class ScoreServiceTest extends TestCase
{
    private ScoreService $scoreService;

    protected function setUp(): void
    {
        $this->scoreService = new ScoreService(new ChainService, new BoardService);
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
        $board = new Board(8, 8);
        $board->set([
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
        ]);
        $game = Game::createFromConfiguration(new ConfigurationDto(
            rows: 8,
            columns: 8,
            stones: 5,
            playerX: 'player X',
            playerO: 'player O',
            playerXisComputer: false,
            playerOisComputer: true
        ));
        $game->board = $board;
        $allRelevantMoves = $this->scoreService->getAllRelevantMoves($game);
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

        $this->assertEquals($expected, $this->move(0, 0));
        $this->assertEquals($expected, $this->move(0, 1));
        $this->assertEquals($expected, $this->move(0, 2));

        $this->assertEquals($expected, $this->move(1, 0));
        $this->assertEquals($expected, $this->move(1, 2));
        
        $this->assertEquals($expected, $this->move(2, 0));
        $this->assertEquals($expected, $this->move(2, 1));
        $this->assertEquals($expected, $this->move(2, 2));

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

        $this->assertEquals($expected, $this->move(1, 1));
    }

    /**
     * @return array<int,array<string,int>>
     */
    private function move(int $row, int $column): array 
    {    
        $game = Game::createFromConfiguration(new ConfigurationDto(
            rows: 3,
            columns: 3,
            stones: 3,
            playerX: 'player X',
            playerO: 'player O',
            playerXisComputer: false,
            playerOisComputer: false
        ));
        $game->board->setField($row, $column, FieldValue::X);
        $game->onTheMove = $game->playerO;
        $allRelevantMoves = $this->scoreService->getAllRelevantMoves($game);
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