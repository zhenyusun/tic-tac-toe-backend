<?php

use Illuminate\Support\Facades\Session;
use Tests\TestCase;

/**
 * Class GameControllerTest
 *
 * This class tests the functionality of the GameController, ensuring
 * that the Tic-Tac-Toe game behaves correctly across various scenarios.
 */
class GameControllerTest extends TestCase
{
    /**
     * Set up the session before each test.
     */
    public function setUp(): void
    {
        parent::setUp();
        Session::start();
    }

    /**
     * Test that the game state is returned correctly when the game is initialized.
     *
     * @return void
     */
    public function testGetGameStateReturnsCorrectInitialState()
    {
        $response = $this->get('/tic-tac-toe/api/game');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'board', 'score', 'currentTurn', 'victory'
        ]);
    }

    /**
     * Test that making a move updates the board and switches the turn correctly.
     *
     * @return void
     */
    public function testMakeMoveUpdatesBoardAndSwitchesTurn()
    {
        $this->get('/tic-tac-toe/api/game');

        $response = $this->post('/tic-tac-toe/api/game/x', ['x' => 0, 'y' => 0]);

        $response->assertStatus(200);
        $response->assertJson([
            'board' => [
                ['x', 'o', ''],
                ['', '', ''],
                ['', '', ''],
            ],
            'currentTurn' => 'x',
            'victory' => '',
        ]);
    }

    /**
     * Test that the computer correctly blocks the human player's winning move.
     *
     * @return void
     */
    public function testComputerBlocksHumanWinningMove()
    {
        $this->get('/tic-tac-toe/api/game');
        $this->post('/tic-tac-toe/api/game/x', ['x' => 1, 'y' => 0]);
        $response = $this->post('/tic-tac-toe/api/game/x', ['x' => 0, 'y' => 1]);

        $response->assertStatus(200);
        $response->assertJson([
            'board' => [
                ['o', 'x', 'o'],
                ['x', '', ''],
                ['', '', ''],
            ],
            'currentTurn' => 'x',
            'victory' => '',
        ]);
    }

    /**
     * Test that the game can be restarted, resetting the board and switching the turn.
     *
     * @return void
     */
    public function testRestartGameResetsBoardAndSwitchesTurn()
    {
        $this->get('/tic-tac-toe/api/game');
        $this->post('/tic-tac-toe/api/game/x', ['x' => 0, 'y' => 0]);

        $response = $this->post('/tic-tac-toe/api/game/restart');

        $response->assertStatus(200);
        $response->assertJson([
            'board' => [
                ['', '', ''],
                ['', '', ''],
                ['', '', ''],
            ],
            'currentTurn' => 'x',
            'victory' => '',
        ]);
    }

    /**
     * Test that the game can be reset, clearing the board and scores.
     *
     * @return void
     */
    public function testResetGameClearsBoardAndScore()
    {
        $this->get('/tic-tac-toe/api/game');
        $this->post('/tic-tac-toe/api/game/x', ['x' => 0, 'y' => 0]);

        $response = $this->delete('/tic-tac-toe/api/game');

        $response->assertStatus(200);
        $response->assertJson([
            'currentTurn' => 'x'
        ]);
    }
}
