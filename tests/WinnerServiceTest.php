<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use TicTacToe\Enums\FieldValue;
use TicTacToe\Entities\EmptyField;
use TicTacToe\Entities\Field;
use TicTacToe\Services\WinnerService;

final class WinnerServiceTest extends TestCase
{
    private WinnerService $winnerService;

    protected function setUp(): void
    {
        $this->winnerService = new WinnerService();
    }

    public function testWinner(): void
    {
        $X = new Field(FieldValue::X);
        $O = new Field(FieldValue::O);
        $_ = new EmptyField();
        $board = [
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
        ];

        $winner = $this->winnerService->winner(4, 4, 3, $board, 3);
        $this->assertEquals(FieldValue::O, $winner);

        $board = [
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
        ];

        $winner = $this->winnerService->winner(4, 4, 3, $board, 3);
        $this->assertEquals(FieldValue::X, $winner);

        $board = [
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
        ];

        $winner = $this->winnerService->winner(4, 4, 3, $board, 3);
        $this->assertEquals(FieldValue::X, $winner);

        $board = [
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
        ];

        $winner = $this->winnerService->winner(4, 4, 3, $board, 3);
        $this->assertEquals(FieldValue::O, $winner);

        $board = [
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
        ];

        $winner = $this->winnerService->winner(4, 4, 3, $board, 3);
        $this->assertEquals(false, $winner);
    }

    public function testDraw(): void
    {
        $X = new Field(FieldValue::X);
        $O = new Field(FieldValue::O);
        $_ = new EmptyField();
        $board = [
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
        ];

        $draw = $this->winnerService->draw(4, 4, 3, $board, 3, FieldValue::O);
        $this->assertEquals(false, $draw);
        $draw = $this->winnerService->draw(4, 4, 3, $board, 3, FieldValue::X);
        $this->assertEquals(false, $draw);

        $board = [
            [
                $_, $X, $X
            ],
            [
                $X, $O, $O
            ],
            [
                $O, $O, $X
            ],
        ];

        $draw = $this->winnerService->draw(3, 3, 3, $board, 3, FieldValue::O);
        $this->assertEquals(true, $draw);
        $draw = $this->winnerService->draw(3, 3, 3, $board, 3, FieldValue::X);
        $this->assertEquals(false, $draw);

        $board = [
            [
                $O, $X, $X
            ],
            [
                $X, $O, $O
            ],
            [
                $O, $O, $X
            ],
        ];

        $draw = $this->winnerService->draw(3, 3, 3, $board, 3, FieldValue::O);
        $this->assertEquals(true, $draw);
        $draw = $this->winnerService->draw(3, 3, 3, $board, 3, FieldValue::X);
        $this->assertEquals(true, $draw);
    }
}