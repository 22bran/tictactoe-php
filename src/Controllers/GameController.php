<?php

namespace TicTacToe\Controllers;

use League\Plates\Engine;
use TicTacToe\Dtos\ConfigurationDto;
use TicTacToe\Dtos\MoveDto;
use TicTacToe\Entities\Game;
use TicTacToe\Attributes\Route;
use TicTacToe\Services\GameService;

class GameController
{
    private GameService $gameService;

    public function __construct(private Engine $templates)
    {
        $this->gameService = new GameService();
    }

    #[Route('GET', '/')]
    public function configuration(): void
    {
        //unset($_SESSION['gameController']);
        echo $this->templates->render('configuration');
    }

    #[Route('GET', '/analyze')]
    public function analyze(): void
    {
        $game = $this->gameService->getGame();
        echo $this->templates->render('analyze', [
            'moves' => $this->gameService->getAllRelevantMoves(
                $game->board,
                $game->onTheMove->stoneType,
                true
            ),
        ]);
    }

    #[Route('POST', '/board')]
    public function board(): void
    {
        $configuration = ConfigurationDto::createFromPostData($_POST);
        $this->gameService->start($configuration);
        $this->renderBoard();
    }

    #[Route('GET', '/move')]
    public function move(): void
    {
        $move = MoveDto::createFromGetData($_GET);
        $this->gameService->placeStone($move->row, $move->column);
        $this->renderBoard();
    }

    #[Route('GET', '/undo-move')]
    public function undoMove(): void
    {
        $this->gameService->undoLastMove();
        $this->renderBoard();
    }

    #[Route('GET', '/restart')]
    public function restart(): void
    {
        $this->gameService->setGame(Game::createFromGame($this->gameService->getGame()));
        $this->renderBoard();
    }

    private function renderBoard(): void
    {
        echo $this->templates->render('board', [
            'game' => $this->gameService->getGame()
        ]);
    }

    public function __destruct()
    {
        $_SESSION['gameController'] = serialize($this);
    }
}
