<?php

use App\Http\Middleware\SupabaseMiddleware;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FriendsController;
use App\Http\Controllers\GameController;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;

Route::middleware(SupabaseMiddleware::class)->group(function () {
    Route::get("/question", [GameController::class, "getQuestion"]);
    Route::get("/my-games", [GameController::class,"getAllMyActiveGames"]);

    Route::post("/create-game", [GameController::class,"createGame"]);
    Route::post("/create-game-friend", [GameController::class,"createGameWithFriend"]);
    Route::post("/set-points", [GameController::class,"updateActiveGame"]);

    Route::get("/my-friends", [FriendsController::class,"getFriends"]);
    Route::post("/add-friend", [FriendsController::class,"addFriend"]);
});
