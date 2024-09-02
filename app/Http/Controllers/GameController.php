<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class GameController extends Controller
{
    /**
     * GameController constructor.
     * Initializes the game if no game session is found.
     */
    public function __construct()
    {
        if (!session()->has('board')) {
            $this->resetGame();
        }
    }

    /**
     * Get the current game state.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getGameState()
    {
        return response()->json([
            'board' => session('board'),
            'score' => session('score'),
            'currentTurn' => session('currentTurn'),
            'victory' => session('victory')
        ], 200);
    }

    /**
     * Handle a player's move.
     *
     * @param \Illuminate\Http\Request $request
     * @param string $piece The piece to place ('x' or 'o').
     * @return \Illuminate\Http\JsonResponse
     */
    public function makeMove(Request $request, $piece)
    {
        $x = $request->input('x');
        $y = $request->input('y');
        $board = session('board');
        $currentTurn = session('currentTurn');

        Log::info('Before move:', ['currentTurn' => $currentTurn, 'piece' => $piece, 'board' => $board]);

        if ($piece !== $currentTurn) {
            return response()->json(['error' => 'Not your turn'], 406);
        }

        if (!isset($board[$x][$y]) || $board[$x][$y] !== '') {
            return response()->json(['error' => 'Position already taken'], 409);
        }

        $board[$x][$y] = $piece;
        session(['board' => $board]);

        if ($this->checkVictory($board, $piece)) {
            session(['victory' => $piece]);
            session(['score.' . $piece => session('score.' . $piece) + 1]);
        } else {
            session(['currentTurn' => $piece === 'x' ? 'o' : 'x']);
        }

        Log::info('After move:', ['currentTurn' => session('currentTurn'), 'board' => $board]);

        return $this->getGameState();
    }

    /**
     * Restart the game, resetting the board and updating the turn based on the last victory.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function restartGame()
    {
        $victory = session('victory', '');
        $board = array_fill(0, 3, array_fill(0, 3, ''));

        session(['board' => $board]);
        session(['victory' => '']);
        session(['currentTurn' => $victory === 'x' ? 'o' : 'x']);
        
        if (!session()->has('score')) {
            session(['score' => ['x' => 0, 'o' => 0]]);
        }

        Log::info('Restarting Game:', session()->all());

        return $this->getGameState();
    }

    /**
     * Reset the game, clearing the board and resetting the scores.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function resetGame()
    {
        session(['board' => array_fill(0, 3, array_fill(0, 3, ''))]);
        session(['score' => ['x' => 0, 'o' => 0]]);
        session(['currentTurn' => 'x']);
        session(['victory' => '']);

        return response()->json(['currentTurn' => session('currentTurn')], 200);
    }

    /**
     * Check if the current piece has won the game.
     *
     * @param array $board The current game board.
     * @param string $piece The piece to check for victory ('x' or 'o').
     * @return bool True if the piece has won, otherwise false.
     */
    private function checkVictory($board, $piece)
    {
        $winningCombinations = [
            [[0, 0], [0, 1], [0, 2]],
            [[1, 0], [1, 1], [1, 2]],
            [[2, 0], [2, 1], [2, 2]],
            [[0, 0], [1, 0], [2, 0]],
            [[0, 1], [1, 1], [2, 1]],
            [[0, 2], [1, 2], [2, 2]],
            [[0, 0], [1, 1], [2, 2]],
            [[0, 2], [1, 1], [2, 0]]
        ];

        foreach ($winningCombinations as $combination) {
            if ($board[$combination[0][0]][$combination[0][1]] === $piece &&
                $board[$combination[1][0]][$combination[1][1]] === $piece &&
                $board[$combination[2][0]][$combination[2][1]] === $piece) {
                return true;
            }
        }

        return false;
    }
}
