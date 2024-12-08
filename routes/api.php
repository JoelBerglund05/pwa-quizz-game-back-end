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

Route::get("/question", [GameController::class, "getQuestion"]);
Route::post("/new-question", [GameController::class,"setQuestion"]);

