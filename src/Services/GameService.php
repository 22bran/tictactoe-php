<?php

namespace TicTacToe\Services;

use Random\Randomizer;
use TicTacToe\Enums\FieldValue;
use TicTacToe\Entities\Player;
use TicTacToe\Dtos\ConfigurationDto;
use TicTacToe\Entities\Game;
use TicTacToe\Dtos\MoveDto;
use TicTacToe\Dtos\PossibleMoveDto;
use TicTacToe\Dtos\PlayersScoreDto;
use TicTacToe\Dtos\LastMoveDto;

class GameService
{
    private WinnerService $winnerService;
    private ScoreService $scoreService;
    private Game $game;

    public function __construct(
    ) {
        $chainService = new ChainService();
        $boardService = new BoardService();
        $this->winnerService = new WinnerService($chainService, $boardService);
        $this->scoreService = new ScoreService($chainService, $boardService);
    }

    public function start(ConfigurationDto $configurationDto): void
    {
        $this->game = Game::createFromConfiguration($configurationDto);
        if ($this->game->onTheMove->isComputer) {
            $this->getMoveForComputer();
        }
    }

    public function placeStone(int $row, int $column): void
    {
        $this->game->moves[] = new MoveDto($row, $column);
        $this->game->board->setField($row, $column, $this->game->onTheMove->stoneType);
        $this->game->remainingMoves--;
        $this->game->winner = $this->fieldValueToPlayer($this->winnerService->winner(
            $this->game,
            $this->game->board,
        ));

        if ($this->game->winner === false && $this->game->remainingMoves === 0) {
            $this->game->draw = true;
        } elseif ($this->game->winner === false && $this->game->remainingMoves > 0) {
            $this->game->draw = $this->winnerService->draw(
                $this->game,
                $this->game->board,
                $this->game->onTheMove->stoneType
            ) && $this->winnerService->draw(
                $this->game,
                $this->game->board,
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
        $this->game->board->setEmpty($lastMove->row, $lastMove->column);
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
        $bestMoves = $this->scoreService->getAllRelevantMoves($this->game);
        $bestMove = null;
        if (count($bestMoves) > 1) {
            foreach ($bestMoves as $move) {
                $winner = $this->winnerService->winner(
                    game: $this->game,
                    board: $move->board,
                );
                if ($winner === $this->game->onTheMove->stoneType) {
                    $bestMove = $move;
                    break;
                }
            }
            if ($bestMove === null) {
                $r = new Randomizer();
                $randomBestMoveKey = $r->pickArrayKeys($bestMoves, 1)[0];
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

    /**
     * @return array<int,PossibleMoveDto>
     */
    public function getMovesForAnalyze(Game $game): array
    {
        $bestMoves = $this->scoreService->getAllRelevantMoves($game);

        return [new PossibleMoveDto(
            maximizer: false,
            board: $game->board,
            scoreObject: new PlayersScoreDto(),
            score: 0,
            stoneType: $game->onTheMove->stoneType,
            index: '',
            move: new LastMoveDto(
                $game->moves[count($game->moves) - 1]->row,
                $game->moves[count($game->moves) - 1]->column,
                ''
            ),
            children: $bestMoves
        )];
    }
}
