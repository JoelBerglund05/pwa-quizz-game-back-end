<?php

use Illuminate\Support\Facades\Route;

Route::get("/", function () {
    return response()->json([
        "Quizz game" => "Welcome to the quizz game of the year!",
    ]);
});

require __DIR__ . "/auth.php";
