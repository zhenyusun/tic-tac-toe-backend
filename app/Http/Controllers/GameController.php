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

        if (!isset($board[$x][$y]) || $board[$x][$y] !== '') {
            return response()->json(['error' => 'Position already taken'], 409);
        }

        if ($piece !== $currentTurn) {
            return response()->json(['error' => 'Not your turn'], 406);
        }

        $board[$x][$y] = $piece;
        session(['board' => $board]);

        if ($this->checkVictory($board, $piece)) {
            session(['victory' => $piece]);
            session(['score.' . $piece => session('score.' . $piece) + 1]);
        } else {
            session(['currentTurn' => $piece === 'x' ? 'o' : 'x']);
            if ($piece === 'x') {
                $this->makeComputerMove($board);
                // Check if the computer won after its move
                if ($this->checkVictory($board, 'o')) {
                    session(['victory' => 'o']);
                    session(['score.o' => session('score.o') + 1]);
                }
            }
        }

        return $this->getGameState();
    }

    /**
     * Restart the game, resetting the board and updating the turn based on the last victory.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function restartGame()
    {
        $board = array_fill(0, 3, array_fill(0, 3, ''));

        session(['board' => $board]);
        session(['victory' => '']);
        session(['currentTurn' => 'x']);

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

    /**
     * Makes a move for the computer player by placing an 'o' in the first empty cell found.
     *
     * @param array &$board The current game board, passed by reference.
     *
     * @return void
     */
    private function makeComputerMove(&$board)
    {
        // Computer: Try to win
        if ($this->makeStrategicMove($board, 'o')) {
            session(['board' => $board]);
            session(['currentTurn' => 'x']);
            return;
        }

        // Computer: Block opponent from winning
        if ($this->makeStrategicMove($board, 'x')) {
            session(['board' => $board]);
            session(['currentTurn' => 'x']);
            return;
        }

        foreach ($board as $x => $row) {
            foreach ($row as $y => $cell) {
                if ($cell === '') {
                    $board[$x][$y] = 'o';
                    session(['board' => $board]);
                    session(['currentTurn' => 'x']);
                    return;
                }
            }
        }
    }

    /**
     * Makes a strategic move for the computer player by checking for winning or blocking opportunities.
     *
     * @param array &$board The current game board, passed by reference.
     * @param string $piece The piece to check for ('x' or 'o').
     *
     * @return bool Returns true if a strategic move was made, false otherwise.
     */
    private function makeStrategicMove(&$board, $piece)
    {
        // Check rows, columns, and diagonals for a winning or blocking move
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
            $values = [
                $board[$combination[0][0]][$combination[0][1]],
                $board[$combination[1][0]][$combination[1][1]],
                $board[$combination[2][0]][$combination[2][1]],
            ];

            if (count(array_filter($values, fn($val) => $val === $piece)) == 2 &&
                count(array_filter($values, fn($val) => $val === '')) == 1) {
                foreach ($combination as $index) {
                    if ($board[$index[0]][$index[1]] === '') {
                        $board[$index[0]][$index[1]] = 'o';
                        return true;
                    }
                }
            }
        }

        return false;
    }
}
