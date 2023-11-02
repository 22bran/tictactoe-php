<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use TicTacToe\Services\ChainService;
use TicTacToe\Enums\FieldValue;
use TicTacToe\Entities\EmptyField;
use TicTacToe\Entities\Field;
use TicTacToe\Dtos\ScoreDto;

final class ChainServiceTest extends TestCase
{
    private ChainService $chainService;

    protected function setUp(): void
    {
        $this->chainService = new ChainService();
    }

    public function testChainWith3FieldsAnd3StonesTarget(): void
    {
        $X = new Field(FieldValue::X);
        $O = new Field(FieldValue::O);
        $_ = new EmptyField();

        $this->assertEquals(new ScoreDto(3, 11, true), $this->chainService->getScore(3, 0, $X, $X, $X));
        $this->assertEquals(new ScoreDto(1, 2), $this->chainService->getScore(3, 2, $_, $_, $O));
        $this->assertEquals(new ScoreDto(2, 7), $this->chainService->getScore(3, 1, $_, $O, $O));
        $this->assertEquals(new ScoreDto(3, 11, true), $this->chainService->getScore(3, 0, $O, $O, $O));
        $this->assertEquals(new ScoreDto(2, 4), $this->chainService->getScore(3, 0, $O, $_, $O));
        $this->assertEquals(new ScoreDto(2, 7), $this->chainService->getScore(3, 0, $O, $O, $_));
        $this->assertEquals(new ScoreDto(1, 2), $this->chainService->getScore(3, 0, $O, $_, $_));

        $this->assertEquals(new ScoreDto(0, 0), $this->chainService->getScore(3, 0, $X, $_, $O));
        $this->assertEquals(new ScoreDto(0, 0), $this->chainService->getScore(3, 0, $X, $O, $O));
        $this->assertEquals(new ScoreDto(0, 0), $this->chainService->getScore(3, 0, $O, $X, $O));
        $this->assertEquals(new ScoreDto(0, 0), $this->chainService->getScore(3, 0, $O, $O, $X));
        $this->assertEquals(new ScoreDto(0, 0), $this->chainService->getScore(3, 0, $O, $X, $_));

        $this->assertEquals(new ScoreDto(3, 11, true), $this->chainService->getScore(3, 1, $X, $X, $X));
        $this->assertEquals(new ScoreDto(1, 2), $this->chainService->getScore(3, 2, $_, $_, $O));
        $this->assertEquals(new ScoreDto(2, 7), $this->chainService->getScore(3, 1, $_, $O, $O));
        $this->assertEquals(new ScoreDto(3, 11, true), $this->chainService->getScore(3, 1, $O, $O, $O));
        $this->assertEquals(new ScoreDto(2, 4), $this->chainService->getScore(3, 0, $O, $_, $O));
        $this->assertEquals(new ScoreDto(2, 7), $this->chainService->getScore(3, 1, $O, $O, $_));
        $this->assertEquals(new ScoreDto(1, 2), $this->chainService->getScore(3, 0, $O, $_, $_));

        $this->assertEquals(new ScoreDto(0, 0), $this->chainService->getScore(3, 2, $X, $_, $O));
        $this->assertEquals(new ScoreDto(0, 0), $this->chainService->getScore(3, 2, $X, $O, $O));
        $this->assertEquals(new ScoreDto(0, 0), $this->chainService->getScore(3, 2, $O, $X, $O));
        $this->assertEquals(new ScoreDto(0, 0), $this->chainService->getScore(3, 2, $O, $O, $X));
        $this->assertEquals(new ScoreDto(0, 0), $this->chainService->getScore(3, 1, $O, $X, $_));
    }

    public function testChainWith5FieldsAnd3StonesTarget(): void
    {
        $X = new Field(FieldValue::X);
        $O = new Field(FieldValue::O);
        $_ = new EmptyField();

        $this->assertEquals(new ScoreDto(0, 0), $this->chainService->getScore(3, 1, $_, $_, $_, $_, $_));
        $this->assertEquals(new ScoreDto(3, 12, true), $this->chainService->getScore(3, 1, $_, $X, $X, $X, $_));
        $this->assertEquals(new ScoreDto(1, 3), $this->chainService->getScore(3, 3, $_, $_, $_, $O, $_));
        $this->assertEquals(new ScoreDto(2, 8), $this->chainService->getScore(3, 2, $_, $_, $O, $O, $_));
        $this->assertEquals(new ScoreDto(3, 12, true), $this->chainService->getScore(3, 1, $_, $O, $O, $O, $_));
        $this->assertEquals(new ScoreDto(2, 5), $this->chainService->getScore(3, 1, $_, $O, $_, $O, $_));
        $this->assertEquals(new ScoreDto(2, 8), $this->chainService->getScore(3, 1, $_, $O, $O, $_, $_));
        $this->assertEquals(new ScoreDto(1, 3), $this->chainService->getScore(3, 1, $_, $O, $_, $_, $_));

        $this->assertEquals(new ScoreDto(1, 3), $this->chainService->getScore(3, 1, $_, $X, $_, $O, $_));
        $this->assertEquals(new ScoreDto(0, 0), $this->chainService->getScore(3, 1, $_, $X, $O, $O, $_));
        $this->assertEquals(new ScoreDto(0, 0), $this->chainService->getScore(3, 1, $_, $O, $X, $O, $_));
        $this->assertEquals(new ScoreDto(2, 7), $this->chainService->getScore(3, 1, $_, $O, $O, $X, $_));
        $this->assertEquals(new ScoreDto(0, 0), $this->chainService->getScore(3, 1, $_, $O, $X, $_, $_));
        $this->assertEquals(new ScoreDto(1, 2), $this->chainService->getScore(3, 2, $_, $O, $X, $_, $_));

        $this->assertEquals(new ScoreDto(3, 12, true), $this->chainService->getScore(3, 1, $_, $X, $X, $X, $_));
        $this->assertEquals(new ScoreDto(1, 3), $this->chainService->getScore(3, 3, $_, $_, $_, $O, $_));
        $this->assertEquals(new ScoreDto(2, 8), $this->chainService->getScore(3, 2, $_, $_, $O, $O, $_));
        $this->assertEquals(new ScoreDto(3, 12, true), $this->chainService->getScore(3, 1, $_, $O, $O, $O, $_));
        $this->assertEquals(new ScoreDto(2, 5), $this->chainService->getScore(3, 1, $_, $O, $_, $O, $_));
        $this->assertEquals(new ScoreDto(2, 8), $this->chainService->getScore(3, 1, $_, $O, $O, $_, $_));
        $this->assertEquals(new ScoreDto(1, 3), $this->chainService->getScore(3, 1, $_, $O, $_, $_, $_));

        $this->assertEquals(new ScoreDto(1, 3), $this->chainService->getScore(3, 1, $_, $X, $_, $O, $_));
        $this->assertEquals(new ScoreDto(0, 0), $this->chainService->getScore(3, 1, $_, $X, $O, $O, $_));
        $this->assertEquals(new ScoreDto(0, 0), $this->chainService->getScore(3, 1, $_, $O, $X, $O, $_));
        $this->assertEquals(new ScoreDto(2, 7), $this->chainService->getScore(3, 1, $_, $O, $O, $X, $_));
        $this->assertEquals(new ScoreDto(0, 0), $this->chainService->getScore(3, 1, $_, $O, $X, $_, $_));

        $this->assertEquals(new ScoreDto(3, 13, true), $this->chainService->getScore(3, 2, $_, $X, $X, $X, $_));
        $this->assertEquals(new ScoreDto(1, 3), $this->chainService->getScore(3, 3, $_, $_, $_, $O, $_));
        $this->assertEquals(new ScoreDto(2, 8), $this->chainService->getScore(3, 2, $_, $_, $O, $O, $_));
        $this->assertEquals(new ScoreDto(3, 13, true), $this->chainService->getScore(3, 2, $_, $O, $O, $O, $_));
        $this->assertEquals(new ScoreDto(2, 5), $this->chainService->getScore(3, 1, $_, $O, $_, $O, $_));
        $this->assertEquals(new ScoreDto(2, 8), $this->chainService->getScore(3, 2, $_, $O, $O, $_, $_));
        $this->assertEquals(new ScoreDto(1, 3), $this->chainService->getScore(3, 1, $_, $O, $_, $_, $_));

        $this->assertEquals(new ScoreDto(1, 3), $this->chainService->getScore(3, 1, $_, $X, $_, $O, $_));
        $this->assertEquals(new ScoreDto(2, 7), $this->chainService->getScore(3, 2, $_, $X, $O, $O, $_));
        $this->assertEquals(new ScoreDto(0, 0), $this->chainService->getScore(3, 2, $_, $O, $X, $O, $_));
        $this->assertEquals(new ScoreDto(2, 7), $this->chainService->getScore(3, 2, $_, $O, $O, $X, $_));
        $this->assertEquals(new ScoreDto(1, 2), $this->chainService->getScore(3, 2, $_, $O, $X, $_, $_));

        $this->assertEquals(new ScoreDto(3, 13), $this->chainService->getScore(5, 2, $_, $X, $X, $X, $_));
        $this->assertEquals(new ScoreDto(4, 18), $this->chainService->getScore(5, 2, $_, $X, $X, $X, $X, $_));
        $this->assertEquals(new ScoreDto(5, 23, true), $this->chainService->getScore(5, 3, $_, $X, $X, $X, $X, $X, $_));
    }

    public function testChainWith8FieldsAnd3StonesTarget(): void
    {
        $X = new Field(FieldValue::X);
        $O = new Field(FieldValue::O);
        $_ = new EmptyField();

        $testChain = [$_, $_, $X, $X, $_, $_, $O, $X];

        $this->assertEquals(new ScoreDto(0, 0), $this->chainService->getScore(3, 0,...$testChain));
        $this->assertEquals(new ScoreDto(0, 0), $this->chainService->getScore(3, 1,...$testChain));
        $this->assertEquals(new ScoreDto(2, 8), $this->chainService->getScore(3, 2,...$testChain));
        $this->assertEquals(new ScoreDto(2, 8), $this->chainService->getScore(3, 3,...$testChain));
        $this->assertEquals(new ScoreDto(0, 0), $this->chainService->getScore(3, 4,...$testChain));
        $this->assertEquals(new ScoreDto(0, 0), $this->chainService->getScore(3, 5,...$testChain));
        $this->assertEquals(new ScoreDto(1, 2), $this->chainService->getScore(3, 6,...$testChain));
        $this->assertEquals(new ScoreDto(0, 0), $this->chainService->getScore(3, 7,...$testChain));
    }

    public function testChainWith8FieldsAnd5StonesTarget(): void
    {
        $X = new Field(FieldValue::X);
        $O = new Field(FieldValue::O);
        $_ = new EmptyField();

        $this->assertEquals(new ScoreDto(0, 0), $this->chainService->getScore(5, 4, $_, $_, $O, $O, $X, $_, $_, $_));
        $this->assertEquals(new ScoreDto(1, 3), $this->chainService->getScore(5, 4, $_, $_, $_, $_, $X, $_, $_, $_));
        $this->assertEquals(new ScoreDto(1, 3), $this->chainService->getScore(5, 4, $_, $_, $_, $_, $O, $_, $_, $_));
        $this->assertEquals(new ScoreDto(2, 8), $this->chainService->getScore(5, 4, $_, $_, $_, $O, $O, $_, $_, $_));
        $this->assertEquals(new ScoreDto(3, 8), $this->chainService->getScore(5, 4, $O, $_, $O, $_, $O, $_, $_, $_));
        $this->assertEquals(new ScoreDto(3, 13), $this->chainService->getScore(5, 4, $_, $_, $O, $O, $O, $_, $_, $_));
        $this->assertEquals(new ScoreDto(4, 18), $this->chainService->getScore(5, 4, $_, $_, $O, $O, $O, $O, $_, $_));
        $this->assertEquals(new ScoreDto(4, 14), $this->chainService->getScore(5, 4, $O, $_, $O, $O, $O, $X, $_, $O));
        $this->assertEquals(new ScoreDto(4, 14), $this->chainService->getScore(5, 4, $O, $_, $X, $O, $O, $O, $_, $O));
    }

    public function testChainWith5FieldsAnd5StonesTarget(): void
    {
        $X = new Field(FieldValue::X);
        $O = new Field(FieldValue::O);
        $_ = new EmptyField();

        $this->assertEquals(new ScoreDto(0, 0), $this->chainService->getScore(5, 0, $_, $_, $_, $_, $_));

        $this->assertEquals(new ScoreDto(1, 2), $this->chainService->getScore(5, 0, $X, $_, $_, $_, $_));
        $this->assertEquals(new ScoreDto(1, 3), $this->chainService->getScore(5, 1, $_, $X, $_, $_, $_));
        $this->assertEquals(new ScoreDto(1, 3), $this->chainService->getScore(5, 2, $_, $_, $X, $_, $_));
        $this->assertEquals(new ScoreDto(1, 3), $this->chainService->getScore(5, 3, $_, $_, $_, $X, $_));
        $this->assertEquals(new ScoreDto(1, 2), $this->chainService->getScore(5, 4, $_, $_, $_, $_, $X));

        $this->assertEquals(new ScoreDto(2, 7), $this->chainService->getScore(5, 0, $X, $X, $_, $_, $_));
        $this->assertEquals(new ScoreDto(2, 5), $this->chainService->getScore(5, 0, $X, $_, $X, $_, $_));
        $this->assertEquals(new ScoreDto(2, 5), $this->chainService->getScore(5, 0, $X, $_, $_, $X, $_));
        $this->assertEquals(new ScoreDto(2, 4), $this->chainService->getScore(5, 0, $X, $_, $_, $_, $X));
        $this->assertEquals(new ScoreDto(2, 8), $this->chainService->getScore(5, 1, $_, $X, $X, $_, $_));
        $this->assertEquals(new ScoreDto(2, 6), $this->chainService->getScore(5, 1, $_, $X, $_, $X, $_));
        $this->assertEquals(new ScoreDto(2, 5), $this->chainService->getScore(5, 1, $_, $X, $_, $_, $X));
        $this->assertEquals(new ScoreDto(2, 8), $this->chainService->getScore(5, 2, $_, $_, $X, $X, $_));
        $this->assertEquals(new ScoreDto(2, 5), $this->chainService->getScore(5, 2, $_, $_, $X, $_, $X));
        $this->assertEquals(new ScoreDto(2, 7), $this->chainService->getScore(5, 3, $_, $_, $_, $X, $X));

        $this->assertEquals(new ScoreDto(3, 12), $this->chainService->getScore(5, 0, $X, $X, $X, $_, $_));
        $this->assertEquals(new ScoreDto(3, 10), $this->chainService->getScore(5, 0, $X, $X, $_, $X, $_));
        $this->assertEquals(new ScoreDto(3, 9), $this->chainService->getScore(5, 0, $X, $X, $_, $_, $X));
        $this->assertEquals(new ScoreDto(3, 10), $this->chainService->getScore(5, 0, $X, $_, $X, $X, $_));
        $this->assertEquals(new ScoreDto(3, 7), $this->chainService->getScore(5, 0, $X, $_, $X, $_, $X));
        $this->assertEquals(new ScoreDto(3, 9), $this->chainService->getScore(5, 0, $X, $_, $_, $X, $X));
        $this->assertEquals(new ScoreDto(3, 12), $this->chainService->getScore(5, 2, $_, $_, $X, $X, $X));
        $this->assertEquals(new ScoreDto(3, 10), $this->chainService->getScore(5, 1, $_, $X, $_, $X, $X));
        $this->assertEquals(new ScoreDto(3, 13), $this->chainService->getScore(5, 1, $_, $X, $X, $X, $_));

        $this->assertEquals(new ScoreDto(4, 17), $this->chainService->getScore(5, 0, $X, $X, $X, $X, $_));
        $this->assertEquals(new ScoreDto(4, 14), $this->chainService->getScore(5, 0, $X, $X, $X, $_, $X));
        $this->assertEquals(new ScoreDto(4, 14), $this->chainService->getScore(5, 0, $X, $X, $_, $X, $X));
        $this->assertEquals(new ScoreDto(4, 14), $this->chainService->getScore(5, 0, $X, $_, $X, $X, $X));
        $this->assertEquals(new ScoreDto(4, 17), $this->chainService->getScore(5, 1, $_, $X, $X, $X, $X));

        $this->assertEquals(new ScoreDto(5, 21, true), $this->chainService->getScore(5, 0, $X, $X, $X, $X, $X));
    }

    public function testChainWith14FieldsAnd5StonesTarget(): void
    {
        $X = new Field(FieldValue::X);
        $O = new Field(FieldValue::O);
        $_ = new EmptyField();

        $this->assertEquals(new ScoreDto(3, 10), $this->chainService->getScore(5, 1, $O, $X, $_, $X, $X, $_, $O, $O, $_, $X, $X, $X, $_, $O));
        $this->assertEquals(new ScoreDto(3, 13), $this->chainService->getScore(5, 9, $O, $X, $_, $X, $X, $_, $O, $O, $_, $X, $X, $X, $_, $O));
    }
}