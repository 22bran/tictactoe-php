<?php

namespace TicTacToe\Services;

use TicTacToe\Enums\FieldValue;
use TicTacToe\Entities\BaseField;
use TicTacToe\Dtos\ScoreDto;

class ChainService
{
    /**
     * @param array<int,int> $line
     */
    private function boostScore(array $line): int
    {
        $score = 0;
        for($i = 0; $i < count($line); $i++) {
            if($line[$i] === 1) {
                $beforeScore = 0;
                $afterScore = 0;
                if (isset($line[$i - 1])) {
                    $beforeScore = $line[$i - 1] === 1 ? 2 : 1;
                }
                if (isset($line[$i + 1])) {
                    $afterScore = $line[$i + 1] === 1 ? 2 : 1;
                }
                $score = $score + $beforeScore + $afterScore + 1;
            }
        }

        return $score;
    }

    public function getScore(int $stones, int $index, BaseField ...$fields): ScoreDto
    {
        $stoneType = $fields[$index]->value();
        $stonesMinus1 = $stones - 1;
        $fieldsCountMinus1 = count($fields) - 1;
        $minIndex = $index - $stonesMinus1 < 0 ? 0 : $index - $stonesMinus1;
        $maxIndex = $index + $stonesMinus1 > $fieldsCountMinus1 ? $fieldsCountMinus1 : $index + $stonesMinus1;
        $line = [];
        foreach($fields as $key => $field) {
            if ($key >= $minIndex && $key <= $maxIndex) {
                if ($field->isEmpty()) {
                    $line[] = 0;
                } elseif ($field->value() === $stoneType) {
                    $line[] = 1;
                } else {
                    if (count($line) >= $stones) {
                        return new ScoreDto(array_sum($line), $this->boostScore($line), $this->isWinner($stones, ...$fields) !== false);
                    }
                    $line = [];
                }
            }
        }

        if (count($line) >= $stones) {
            return new ScoreDto(array_sum($line), $this->boostScore($line), $this->isWinner($stones, ...$fields) !== false);
        } else {
            return new ScoreDto();
        }
    }

    public function isWinner(int $stonesCount, BaseField ...$fields): FieldValue|false
    {
        $xCount = 0;
        $oCount = 0;
        foreach($fields as $field) {
            if ($field->isEmpty()) {
                $xCount = 0;
                $oCount = 0;
                continue;
            }
            if ($field->value() === FieldValue::X) {
                $oCount = 0;
                $xCount++;
                if ($xCount === $stonesCount) {
                    return FieldValue::X;
                }
            } else {
                $xCount = 0;
                $oCount++;
                if($oCount === $stonesCount) {
                    return FieldValue::O;
                }
            }
        }
        return false;
    }
}
