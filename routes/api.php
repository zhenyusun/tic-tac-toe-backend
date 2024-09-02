<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GameController;

/**
 * Group routes that require the 'web' middleware.
 */
Route::middleware('web')->group(function () {

    /**
     * Get the current game state.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    Route::get('/game', [GameController::class, 'getGameState']);

    /**
     * Restart the game, resetting the board and possibly the scores.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    Route::post('/game/restart', [GameController::class, 'restartGame']);

    /**
     * Make a move in the game by placing a piece.
     *
     * @param string $piece
     * @return \Illuminate\Http\JsonResponse
     */
    Route::post('/game/{piece}', [GameController::class, 'makeMove']);

    /**
     * Reset the game, clearing the board and scores.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    Route::delete('/game', [GameController::class, 'resetGame']);
});
