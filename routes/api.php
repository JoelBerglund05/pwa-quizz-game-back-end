<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\FriendsController;
use App\Http\Controllers\GameController;

/*Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
    });*/



Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::group(["middleware"=> ["auth:sanctum"]], function () {

    /*
    1. /question fixa så att du inte kan kalla utan ett aktivt spel (Max en gång).
    2.
    */

    Route::get("/question", [GameController::class, "getQuestion"]);
    Route::get("/my-games", [GameController::class,"getAllMyActiveGames"]);

    Route::post("/create-game", [GameController::class,"createGame"]);
    Route::post("/create-game-friend", [GameController::class,"createGameWithFriend"]);
    Route::post("/set-points", [GameController::class,"updateActiveGame"]);

    Route::get("/my-friends", [FriendsController::class,"getFriends"]);
    Route::post("/add-friend", [FriendsController::class,"addFriend"]);
});
