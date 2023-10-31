<?php

namespace TicTacToe\Services;

use TicTacToe\Dtos\MovesRelevancyDto;
use TicTacToe\Enums\FieldValue;
use TicTacToe\Entities\BaseField;
use TicTacToe\Dtos\PlayersScoreDto;
use TicTacToe\Dtos\AllDirectionsScoreDto;
use TicTacToe\Dtos\LastMoveDto;
use TicTacToe\Dtos\PossibleMoveDto;
use TicTacToe\Dtos\ScoreDto;
use TicTacToe\Entities\Board;
use TicTacToe\Entities\Game;
use TicTacToe\Helpers\ColumnsHelper;

class ScoreService
{
    public function __construct(
        private readonly ChainService $chainService,
        private readonly BoardService $boardService
    ) {}

    public function possibleToWinScore(Game $game, Board $board, int $row, int $column, FieldValue $fieldValue): AllDirectionsScoreDto
    {
        $modifiedBoard = clone $board;
        $modifiedBoard->setField($row, $column, $fieldValue);

        $scoreHorizontal = $this->chainService->getScore($game->stones, $column, ...$modifiedBoard->get()[$row]);

        $columns = ColumnsHelper::getColumns($game->columns, $modifiedBoard);
        $scoreVertical = $this->chainService->getScore($game->stones, $row, ...$columns[$column]);

        $diagonalLeft = $this->boardService->getDiagonalForField($modifiedBoard, $row, $column, false);
        $scoreDiagonalLeft = $this->chainService->getScore($game->stones, $diagonalLeft->index, ...$diagonalLeft->diagonal);

        $diagonalRight = $this->boardService->getDiagonalForField($modifiedBoard, $row, $column, true);
        $scoreDiagonalRight = $this->chainService->getScore($game->stones, $diagonalRight->index, ...$diagonalRight->diagonal);

        return new AllDirectionsScoreDto($scoreHorizontal, $scoreVertical, $scoreDiagonalLeft, $scoreDiagonalRight);
    }

    /**
     * @param array<int,PossibleMoveDto> $tree
     * @return array<int,PossibleMoveDto>
     */
    private function createTreeOfMoves(Game $game, int $depth, bool $maximizer, array $tree = []): array
    {
        foreach ($tree as $key => $node) {
            $nextStoneType = $node->stoneType === FieldValue::X ? FieldValue::O : FieldValue::X;
            $children = $this->createNode($game, $node->board, $nextStoneType, $node->scoreObject, $maximizer, $node->index);
            if ($depth > 0) {
                $tree[$key]->children = $this->createTreeOfMoves($game, $depth - 1, !$maximizer, $children);
            }
        }

        return $tree;
    }

    /**
     * @param array<int,PossibleMoveDto> $queue
     * @param array<int,array<int,array<int,int>>> $output
     * @return array<int,int>
     */
    private function findBestScoreForParentBranches(array $queue, array $output = []): array
    {
        $node = array_shift($queue);
        if ($node === null) {
            return [];
        }
        foreach ($node->children as $child) {
            $queue[] = $child;
        }
        $isLast = count($queue) === 0;
        $index = explode('-', $node->index);
        $level = count($index);
        $levelBefore = $isLast ? $level : $level - 1;
        $isMaximizer = $isLast ? $node->maximizer === true : $node->maximizer === false;
        $parentIndex = intval($index[0]);
        $score = $node->score;
        if($isLast) {
            $output[$level][$score][] = $parentIndex;
        }

        if ((!isset($output[$level]) && isset($output[$levelBefore]) && count($output[$levelBefore]) > 1) || $isLast) {
            if ($isMaximizer) {
                $max = max(array_keys($output[$levelBefore]));
                return $output[$levelBefore][$max];
            } else {
                $min = min(array_keys($output[$levelBefore]));
                return $output[$levelBefore][$min];
            }
        }

        $output[$level][$score][] = $parentIndex;

        return $this->findBestScoreForParentBranches($queue, $output);
    }

    /**
     * @param array<int,array<int,int>> $player
     */
    private function getMovesRelevancyScoreForPlayer(array $player, int $index, int $max): int
    {
        if(isset($player[$index])) {
            if($index === $max) {
                return array_sum($player[$index]) * $index;
            } else {
                return $index;
            }
        }
        return 0;
    }

    private function getMovesRelevancyScoreFromArray(PlayersScoreDto $movesRelevancyScore): int
    {
        $player = $movesRelevancyScore->player;
        $nextPlayer = $movesRelevancyScore->nextPlayer;
        $finalScorePlayer = 0;
        $finalScoreNextPlayer = 0;
        $maxStonesPlayer = max(array_keys($player));
        $maxStonesNextPlayer = max(array_keys($nextPlayer));
        $index = max([$maxStonesPlayer, $maxStonesNextPlayer]);
        $max = $index;
        while($index > 0) {
            $finalScorePlayer += $this->getMovesRelevancyScoreForPlayer($player, $index, $max);
            $finalScoreNextPlayer += $this->getMovesRelevancyScoreForPlayer($nextPlayer, $index, $max);
            $index--;
        }

        return ($finalScorePlayer + $finalScoreNextPlayer) * $max;
    }

    private function getMovesRelevancy(Game $game, Board $board, FieldValue $stoneType, FieldValue $nextStoneType, PlayersScoreDto $lastScore): MovesRelevancyDto
    {
        $movesRelevancy = [];
        $movesRelevancyDetail = [];
        for ($row = 0; $row < $game->rows; $row++) {
            for ($column = 0; $column < $game->columns; $column++) {
                if ($board->isEmpty($row, $column)) {
                    $scorePlayer = $this->possibleToWinScore($game, $board, $row, $column, $stoneType);
                    $scoreNextPlayer = $this->possibleToWinScore($game, $board, $row, $column, $nextStoneType);
                    $orderedScoresPlayer = $this->orderScores($scorePlayer);
                    $orderedScoresNextPlayer = $this->orderScores($scoreNextPlayer);

                    $scoreObj = new PlayersScoreDto($orderedScoresPlayer, $orderedScoresNextPlayer);
                    $score = $this->getMovesRelevancyScoreFromArray($scoreObj);
                    $movesRelevancyDetail[$row][$column] = $this->getScoreDetail($stoneType, $scorePlayer) . "\n" . $this->getScoreDetail($nextStoneType, $scoreNextPlayer) . "\nTotal score: $score";

                    if ($score >= $this->getMovesRelevancyScoreFromArray($lastScore)) {
                        $movesRelevancy[$row][$column] = $scoreObj;
                    }
                }
            }
        }
        return new MovesRelevancyDto($movesRelevancy, $movesRelevancyDetail);
    }

    private function getScoreDetail(FieldValue $stoneType, AllDirectionsScoreDto $scorePlayer): string
    {
        return join("\t", [
            $stoneType->toString(),
            $this->getScoreDetailForDirection($scorePlayer->horizontal, 'H'),
            $this->getScoreDetailForDirection($scorePlayer->vertical, 'V'),
            $this->getScoreDetailForDirection($scorePlayer->diagonalLeft, 'L'),
            $this->getScoreDetailForDirection($scorePlayer->diagonalRight, 'R')
        ]);
    }

    /**
     * @return array<int,array<int,int>>
     */
    private function orderScores(AllDirectionsScoreDto $scores): array
    {
        $ordered = [];
        foreach($scores->toArray() as $score) {
            $ordered[$score->stonesCount][] = $score->score;
        }
        ksort($ordered);
        return $ordered;
    }

    private function isEmpty(BaseField $field): bool
    {
        return $field->isEmpty();
    }

    private function getScoreDetailForDirection(ScoreDto $score, string $id): string
    {
        return "$id:" . $score->score . ' (' . $score->stonesCount . ")";
    }

    /**
     * @param array<int,array<int,PlayersScoreDto>> $movesRelevancy
     * @return array<int,array<int,PlayersScoreDto>>
     */
    private function getMovesWithBestRelevancy(Board $board, array $movesRelevancy): array
    {
        $bestRelevancyScore = new PlayersScoreDto();
        $mostRelevantMoves = [];

        foreach ($board->get() as $row => $rowData) {
            foreach ($rowData as $column => $cell) {
                if ($this->isEmpty($cell) && isset($movesRelevancy[$row][$column])) {
                    $currentScore = $this->getMovesRelevancyScoreFromArray($movesRelevancy[$row][$column]);
                    $bestScore = $this->getMovesRelevancyScoreFromArray($bestRelevancyScore);
                    if ($currentScore > $bestScore) {
                        $bestRelevancyScore = $movesRelevancy[$row][$column];
                        $mostRelevantMoves = [];
                        $mostRelevantMoves[$row][$column] = $bestRelevancyScore;
                    } elseif ($currentScore === $bestScore) {
                        $mostRelevantMoves[$row][$column] = $bestRelevancyScore;
                    }
                }
            }
        }

        return $mostRelevantMoves;
    }

    /**
     * @return array<int,PossibleMoveDto>
     */
    private function createNode(Game $game, Board $board, FieldValue $stoneType, PlayersScoreDto $score, bool $maximizer, ?string $parentIndex): array
    {
        $nextStoneType = $stoneType === FieldValue::X ? FieldValue::O : FieldValue::X;
        $movesRelevancyWithDetail = $this->getMovesRelevancy($game, $board, $stoneType, $nextStoneType, $score);
        $movesRelevancy = $movesRelevancyWithDetail->movesRelevancy;
        $movesRelevancyDetail = $movesRelevancyWithDetail->movesRelevancyDetail;
        $mostRelevantMoves = $this->getMovesWithBestRelevancy($board, $movesRelevancy);
        $node = [];
        foreach($mostRelevantMoves as $row => $columns) {
            foreach($columns as $column => $scoreObject) {
                $score = $this->getMovesRelevancyScoreFromArray($scoreObject);
                $scoreDetail = isset($movesRelevancyDetail[$row][$column]) ? $movesRelevancyDetail[$row][$column] : '';
                $newBoard = clone $board;
                $newBoard->setField($row, $column, $stoneType);
                $node[] = new PossibleMoveDto(
                    maximizer: $maximizer,
                    board: $newBoard,
                    scoreObject: $scoreObject,
                    score: $score,
                    stoneType: $stoneType,
                    index: strval($parentIndex !== null ? $parentIndex . '-' . count($node) : count($node)),
                    move: new LastMoveDto($row, $column, $scoreDetail)
                );
            }
        }

        return $node;
    }

    /**
     * @return array<int,PossibleMoveDto>
     */
    public function getAllRelevantMoves(Game $game): array
    {
        $board = $game->board;
        $stoneType = $game->onTheMove->stoneType;

        $root = $this->createNode($game, $board, $stoneType, new PlayersScoreDto(), true, null);
        $moves = $this->createTreeOfMoves($game, 2, false, $root);
        if (count($moves) > 1) {
            $bestParentBranchesScore = $this->findBestScoreForParentBranches($moves);
            $bestMoves = array_filter($moves, function ($data, $key) use ($bestParentBranchesScore) { return in_array($key, $bestParentBranchesScore); }, ARRAY_FILTER_USE_BOTH);
        } else {
            $bestMoves = $moves;
        }

        return $bestMoves;
    }
}
