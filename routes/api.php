<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\GameController;

/*Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
    });*/



    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

Route::group(["middleware"=> ["auth:sanctum"]], function () {
    Route::get("/", function (Request $request) {
        return [
            "MINE!!!" => ["Attans!", "NeJ!", "vArFöR?!?!"],
            "Säker att det funkar?" => "3",
            "request" => $request
        ];
    });

    Route::get("/question", [GameController::class, "getQuestion"]);

    Route::post("/create-game", [GameController::class,"createGame"]);
});
