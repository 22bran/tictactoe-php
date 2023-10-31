<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use TicTacToe\Services\BoardService;
use TicTacToe\Enums\FieldValue;
use TicTacToe\Entities\EmptyField;
use TicTacToe\Entities\Field;
use TicTacToe\Dtos\DiagonalForFieldDto;
use TicTacToe\Entities\Board;

final class BoardServiceTest extends TestCase
{
    private BoardService $boardService;

    protected function setUp(): void
    {
        $this->boardService = new BoardService();
    }

    public function testGetDiagonal(): void
    {
        $X = new Field(FieldValue::X);
        $O = new Field(FieldValue::O);
        $_ = new EmptyField();
        $board = new Board(8, 8);
        $board->set([
            [
                $_, $_, $_, $_, $_, $_, $_, $_
            ],
            [
                $_, $_, $O, $_, $O, $_, $_, $_
            ],
            [
                $_, $_, $O, $X, $X, $_, $_, $_
            ],
            [
                $_, $_, $_, $O, $X, $_, $_, $_
            ],
            [
                $_, $_, $_, $_, $X, $X, $_, $_
            ],
            [
                $_, $_, $_, $_, $O, $_, $X, $_
            ],
            [
                $_, $_, $_, $_, $_, $_, $_, $_
            ],
            [
                $_, $_, $_, $_, $_, $_, $_, $_
            ],
        ]);

        $diagonal = $this->boardService->getDiagonal($board, 0, 1, false);
        $this->assertEquals([$_,$O,$X,$X,$X,$X,$_], $diagonal);

        $diagonal = $this->boardService->getDiagonal($board, 5, 4, false);
        $this->assertEquals([$O,$_,$_], $diagonal);

        $diagonal = $this->boardService->getDiagonal($board, 0, 5, true);
        $this->assertEquals([$_,$O,$X,$_,$_,$_], $diagonal);

        $diagonal = $this->boardService->getDiagonal($board, 4, 7, true);
        $this->assertEquals([$_,$X,$_,$_], $diagonal);
    }

    public function testGetDiagonalForField(): void
    {
        $X = new Field(FieldValue::X);
        $O = new Field(FieldValue::O);
        $_ = new EmptyField();
        $board = new Board(8, 8);
        $board->set([
            [
                $_, $_, $_, $_, $_, $_, $_, $_
            ],
            [
                $_, $_, $O, $_, $O, $_, $_, $_
            ],
            [
                $_, $_, $O, $X, $X, $_, $_, $_
            ],
            [
                $_, $_, $_, $O, $X, $_, $_, $_
            ],
            [
                $_, $_, $_, $_, $X, $X, $_, $_
            ],
            [
                $_, $_, $_, $_, $O, $_, $X, $_
            ],
            [
                $_, $_, $_, $_, $_, $_, $_, $_
            ],
            [
                $_, $_, $_, $_, $_, $_, $_, $_
            ],
        ]);

        $diagonal = $this->boardService->getDiagonalForField($board, 2, 3, false);
        $this->assertEquals(new DiagonalForFieldDto([$_,$O,$X,$X,$X,$X,$_], 2), $diagonal);

        $diagonal = $this->boardService->getDiagonalForField($board, 5, 4, false);
        $this->assertEquals(new DiagonalForFieldDto([$_,$_,$_,$_,$O,$_,$_], 4), $diagonal);

        $diagonal = $this->boardService->getDiagonalForField($board, 1, 4, true);
        $this->assertEquals(new DiagonalForFieldDto([$_,$O,$X,$_,$_,$_], 1), $diagonal);

        $diagonal = $this->boardService->getDiagonalForField($board, 5, 6, true);
        $this->assertEquals(new DiagonalForFieldDto([$_,$X,$_,$_], 1), $diagonal);
    }
}