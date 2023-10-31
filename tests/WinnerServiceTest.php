<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use TicTacToe\Dtos\ConfigurationDto;
use TicTacToe\Entities\Board;
use TicTacToe\Enums\FieldValue;
use TicTacToe\Entities\EmptyField;
use TicTacToe\Entities\Field;
use TicTacToe\Entities\Game;
use TicTacToe\Services\BoardService;
use TicTacToe\Services\ChainService;
use TicTacToe\Services\WinnerService;

final class WinnerServiceTest extends TestCase
{
    private WinnerService $winnerService;

    protected function setUp(): void
    {
        $this->winnerService = new WinnerService(new ChainService, new BoardService);
    }

    public function testWinner(): void
    {
        $X = new Field(FieldValue::X);
        $O = new Field(FieldValue::O);
        $_ = new EmptyField();
        $game = Game::createFromConfiguration(new ConfigurationDto(
            rows: 4,
            columns: 4,
            stones: 3,
            playerX: 'player X',
            playerO: 'player O',
            playerXisComputer: false,
            playerOisComputer: true
        ));
        $board = new Board(4, 4);
        $board->set([
            [
                $_, $_, $_, $_
            ],
            [
                $_, $O, $O, $X
            ],
            [
                $_, $_, $O, $X
            ],
            [
                $_, $X, $X, $O
            ],
        ]);
        
        $winner = $this->winnerService->winner($game, $board);
        $this->assertEquals(FieldValue::O, $winner);

        $board->set([
            [
                $O, $_, $_, $X
            ],
            [
                $_, $_, $O, $X
            ],
            [
                $_, $_, $O, $X
            ],
            [
                $_, $X, $X, $O
            ],
        ]);

        $winner = $this->winnerService->winner($game, $board);
        $this->assertEquals(FieldValue::X, $winner);

        $board->set([
            [
                $O, $_, $_, $X
            ],
            [
                $O, $_, $O, $X
            ],
            [
                $_, $_, $X, $X
            ],
            [
                $_, $X, $X, $O
            ],
        ]);

        $winner = $this->winnerService->winner($game, $board);
        $this->assertEquals(FieldValue::X, $winner);

        $board->set([
            [
                $O, $_, $_, $_
            ],
            [
                $O, $O, $O, $X
            ],
            [
                $_, $_, $X, $X
            ],
            [
                $_, $X, $X, $O
            ],
        ]);

        $winner = $this->winnerService->winner($game, $board);
        $this->assertEquals(FieldValue::O, $winner);

        $board->set([
            [
                $O, $_, $_, $_
            ],
            [
                $O, $X, $O, $X
            ],
            [
                $_, $_, $O, $X
            ],
            [
                $_, $X, $X, $O
            ],
        ]);

        $winner = $this->winnerService->winner($game, $board);
        $this->assertEquals(false, $winner);
    }

    public function testDraw(): void
    {
        $X = new Field(FieldValue::X);
        $O = new Field(FieldValue::O);
        $_ = new EmptyField();
        $game = Game::createFromConfiguration(new ConfigurationDto(
            rows: 4,
            columns: 4,
            stones: 3,
            playerX: 'player X',
            playerO: 'player O',
            playerXisComputer: false,
            playerOisComputer: true
        ));
        $board = new Board(4, 4);
        $board->set([
            [
                $_, $_, $_, $_
            ],
            [
                $_, $O, $O, $X
            ],
            [
                $_, $_, $O, $X
            ],
            [
                $_, $X, $X, $O
            ],
        ]);

        $draw = $this->winnerService->draw($game, $board, FieldValue::O);
        $this->assertEquals(false, $draw);
        $draw = $this->winnerService->draw($game, $board, FieldValue::X);
        $this->assertEquals(false, $draw);
        $game = Game::createFromConfiguration(new ConfigurationDto(
            rows: 3,
            columns: 3,
            stones: 3,
            playerX: 'player X',
            playerO: 'player O',
            playerXisComputer: false,
            playerOisComputer: true
        ));
        $board = new Board(3, 3);
        $board->set([
            [
                $_, $X, $X
            ],
            [
                $X, $O, $O
            ],
            [
                $O, $O, $X
            ],
        ]);

        $draw = $this->winnerService->draw($game, $board, FieldValue::O);
        $this->assertEquals(true, $draw);
        $draw = $this->winnerService->draw($game, $board, FieldValue::X);
        $this->assertEquals(false, $draw);

        $board->set([
            [
                $O, $X, $X
            ],
            [
                $X, $O, $O
            ],
            [
                $O, $O, $X
            ],
        ]);

        $draw = $this->winnerService->draw($game, $board, FieldValue::O);
        $this->assertEquals(true, $draw);
        $draw = $this->winnerService->draw($game, $board, FieldValue::X);
        $this->assertEquals(true, $draw);
    }
}