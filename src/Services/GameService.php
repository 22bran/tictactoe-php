<?php

namespace TicTacToe\Services;

use TicTacToe\Dtos\MovesRelevancyDto;
use TicTacToe\Enums\FieldValue;
use TicTacToe\Entities\Player;
use TicTacToe\Entities\BaseField;
use TicTacToe\Entities\Field;
use TicTacToe\Dtos\PlayersScoreDto;
use TicTacToe\Dtos\AllDirectionsScoreDto;
use TicTacToe\Dtos\ConfigurationDto;
use TicTacToe\Dtos\LastMoveDto;
use TicTacToe\Dtos\PossibleMoveDto;
use TicTacToe\Dtos\ScoreDto;
use TicTacToe\Entities\Game;
use TicTacToe\Dtos\MoveDto;
use TicTacToe\Entities\EmptyField;

class GameService
{
    private ChainService $chainService;
    private BoardService $boardService;
    private WinnerService $winnerService;
    private Game $game;

    public function __construct(
    ) {
        $this->boardService = new BoardService();
        $this->chainService = new ChainService();
        $this->winnerService = new WinnerService();
    }

    public function start(ConfigurationDto $configurationDto): void
    {
        $this->game = Game::createFromConfiguration($configurationDto);
        if ($this->game->onTheMove->isComputer) {
            $this->getMoveForComputer();
        }
    }

    /**
     * @param array<int,array<int,BaseField>> $board
     */
    public function possibleToWinScore(array $board, int $row, int $column, FieldValue $fieldValue): AllDirectionsScoreDto
    {
        $modifiedBoard = $board;
        $modifiedBoard[$row][$column] = new Field($fieldValue);

        $scoreHorizontal = $this->chainService->getScore($this->game->stones, $column, ...$modifiedBoard[$row]);

        $columns = $this->winnerService->getColumns($this->game->columns, $modifiedBoard);
        $scoreVertical = $this->chainService->getScore($this->game->stones, $row, ...$columns[$column]);

        $diagonalLeft = $this->boardService->getDiagonalForField($modifiedBoard, $row, $column, false);
        $scoreDiagonalLeft = $this->chainService->getScore($this->game->stones, $diagonalLeft->index, ...$diagonalLeft->diagonal);

        $diagonalRight = $this->boardService->getDiagonalForField($modifiedBoard, $row, $column, true);
        $scoreDiagonalRight = $this->chainService->getScore($this->game->stones, $diagonalRight->index, ...$diagonalRight->diagonal);

        return new AllDirectionsScoreDto($scoreHorizontal, $scoreVertical, $scoreDiagonalLeft, $scoreDiagonalRight);
    }

    private function isEmpty(BaseField $field): bool
    {
        return $field->isEmpty();
    }

    private function getScoreDetailForDirection(ScoreDto $score, string $id): string
    {
        return "$id:" . $score->score .' (' . $score->stonesCount . ")";
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

    /**
     * @param array<int,array<int,BaseField>> $board
     */
    private function getMovesRelevancy(array $board, FieldValue $stoneType, FieldValue $nextStoneType, PlayersScoreDto $lastScore): MovesRelevancyDto
    {
        $movesRelevancy = [];
        $movesRelevancyDetail = [];
        for ($row = 0; $row < $this->game->rows; $row++) {
            for ($column = 0; $column < $this->game->columns; $column++) {
                if ($this->isEmpty($board[$row][$column])) {
                    $scorePlayer = $this->possibleToWinScore($board, $row, $column, $stoneType);
                    $scoreNextPlayer = $this->possibleToWinScore($board, $row, $column, $nextStoneType);
                    $orderedScoresPlayer = $this->orderScores($scorePlayer);
                    $orderedScoresNextPlayer = $this->orderScores($scoreNextPlayer);

                    $scoreObj = new PlayersScoreDto($orderedScoresPlayer, $orderedScoresNextPlayer);
                    $score = $this->getMovesRelevancyScoreFromArray($scoreObj);
                    $movesRelevancyDetail[$row][$column] = $this->getScoreDetail($stoneType, $scorePlayer). "\n" . $this->getScoreDetail($nextStoneType, $scoreNextPlayer) . "\nTotal score: $score";

                    if ($score >= $this->getMovesRelevancyScoreFromArray($lastScore)) {
                        $movesRelevancy[$row][$column] = $scoreObj;
                    }
                }
            }
        }
        return new MovesRelevancyDto($movesRelevancy, $movesRelevancyDetail);
    }

    /**
     * @param array<int,array<int,BaseField>> $board
     * @param array<int,array<int,PlayersScoreDto>> $movesRelevancy
     * @return array<int,array<int,PlayersScoreDto>>
     */
    private function getMovesWithBestRelevancy(array $board, array $movesRelevancy): array
    {
        $bestRelevancyScore = new PlayersScoreDto();
        $mostRelevantMoves = [];

        foreach ($board as $row => $rowData) {
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
     * @param array<int,array<int,BaseField>> $board
     * @return array<int,PossibleMoveDto>
     */
    private function createNode(?string $parentIndex, array $board, FieldValue $stoneType, PlayersScoreDto $score, bool $maximizer): array
    {
        $nextStoneType = $stoneType === FieldValue::X ? FieldValue::O : FieldValue::X;
        $movesRelevancyWithDetail = $this->getMovesRelevancy($board, $stoneType, $nextStoneType, $score);
        $movesRelevancy = $movesRelevancyWithDetail->movesRelevancy;
        $movesRelevancyDetail = $movesRelevancyWithDetail->movesRelevancyDetail;
        $mostRelevantMoves = $this->getMovesWithBestRelevancy($board, $movesRelevancy);
        $node = [];
        foreach($mostRelevantMoves as $row => $columns) {
            foreach($columns as $column => $scoreObject) {
                $score = $this->getMovesRelevancyScoreFromArray($scoreObject);
                $scoreDetail = isset($movesRelevancyDetail[$row][$column]) ? $movesRelevancyDetail[$row][$column] : '';
                $newBoard = $board;
                $newBoard[$row][$column] = new Field($stoneType);
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
     * @param array<int,PossibleMoveDto> $tree
     * @return array<int,PossibleMoveDto>
     */
    private function createTreeOfMoves(int $depth, bool $maximizer, array $tree = []): array
    {
        foreach ($tree as $key => $node) {
            $nextStoneType = $node->stoneType === FieldValue::X ? FieldValue::O : FieldValue::X;
            $children = $this->createNode($node->index, $node->board, $nextStoneType, $node->scoreObject, $maximizer);
            if ($depth > 0) {
                $tree[$key]->children = $this->createTreeOfMoves($depth - 1, !$maximizer, $children);
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
     * @param array<int,array<int,BaseField>> $board
     * @return array<int,PossibleMoveDto>
     */
    public function getAllRelevantMoves(array $board, FieldValue $stoneType, bool $withCurrentBoard = false): array
    {
        $root = $this->createNode(null, $board, $stoneType, new PlayersScoreDto(), true);
        $moves = $this->createTreeOfMoves(2, false, $root);
        if (count($moves) > 1) {
            $bestParentBranchesScore = $this->findBestScoreForParentBranches($moves);
            $bestMoves = array_filter($moves, function ($data, $key) use ($bestParentBranchesScore) { return in_array($key, $bestParentBranchesScore); }, ARRAY_FILTER_USE_BOTH);
        } else {
            $bestMoves = $moves;
        }
        if ($withCurrentBoard) {
            return [new PossibleMoveDto(
                maximizer: false,
                board: $this->game->board,
                scoreObject: new PlayersScoreDto(),
                score: 0,
                stoneType: $stoneType,
                index: '',
                move: new LastMoveDto(
                    $this->game->moves[count($this->game->moves) - 1]->row,
                    $this->game->moves[count($this->game->moves) - 1]->column,
                    ''
                ),
                children: $bestMoves
            )];
        }

        return $bestMoves;
    }

    public function placeStone(int $row, int $column): void
    {
        $this->game->moves[] = new MoveDto($row, $column);
        $this->game->board[$row][$column] = new Field($this->game->onTheMove->stoneType);
        $this->game->remainingMoves--;
        $this->game->winner = $this->fieldValueToPlayer($this->winnerService->winner(
            $this->game->rows,
            $this->game->columns,
            $this->game->stones,
            $this->game->board,
            $this->game->stones
        ));

        if ($this->game->winner === false && $this->game->remainingMoves === 0) {
            $this->game->draw = true;
        } elseif ($this->game->winner === false && $this->game->remainingMoves > 0) {
            $this->game->draw = $this->winnerService->draw(
                $this->game->rows,
                $this->game->columns,
                $this->game->stones,
                $this->game->board,
                $this->game->stones,
                $this->game->onTheMove->stoneType
            ) && $this->winnerService->draw(
                $this->game->rows,
                $this->game->columns,
                $this->game->stones,
                $this->game->board,
                $this->game->stones,
                $this->game->onTheMove->stoneType === FieldValue::X ? FieldValue::O : FieldValue::X
            );
        }
        $this->game->switchPlayer();
        if ($this->game->winner !== false || $this->game->draw) {
            $this->game->endTime = time();
            return;
        }
        if ($this->game->onTheMove->isComputer) {
            $this->getMoveForComputer();
        }
    }

    private function fieldValueToPlayer(FieldValue|false $value): Player|false
    {
        if ($value === false) {
            return false;
        }
        return $value === FieldValue::X ? $this->game->playerX : $this->game->playerO;
    }

    public function undoLastMove(): void
    {
        if(count($this->game->moves) === 0) {
            return;
        }
        $lastMove = array_pop($this->game->moves);
        $this->game->board[$lastMove->row][$lastMove->column] = new EmptyField();
        $this->game->remainingMoves++;
        $this->game->winner = false;
        $this->game->draw = false;
        $this->game->endTime = null;
        $this->game->switchPlayer();
    }

    public function getGame(): Game
    {
        return $this->game;
    }

    public function setGame(Game $game): void
    {
        $this->game = $game;
    }

    public function getMoveForComputer(): void
    {
        $bestMoves = $this->getAllRelevantMoves($this->game->board, $this->game->onTheMove->stoneType);
        $bestMove = null;
        if (count($bestMoves) > 1) {
            foreach ($bestMoves as $move) {
                $winner = $this->winnerService->winner(
                    rows: $this->game->rows,
                    columns: $this->game->columns,
                    stones: $this->game->stones,
                    board: $move->board,
                    stonesCount: $this->game->stones
                );
                if ($winner === $this->game->onTheMove->stoneType) {
                    $bestMove = $move;
                    break;
                }
            }
            if ($bestMove === null) {
                $randomBestMoveKey = array_rand($bestMoves, 1);
                $bestMove = $bestMoves[$randomBestMoveKey];
            }
        } else {
            $bestMove = array_shift($bestMoves);
            if ($bestMove === null) {
                return;
            }
        }

        $this->placeStone($bestMove->move->row, $bestMove->move->column);
    }
}
