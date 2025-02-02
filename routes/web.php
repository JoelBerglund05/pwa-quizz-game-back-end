<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json(["test"=>"ing"]);
});

Route::post('/t', function () {
    return response()->json(["hej"=>"tja"]);
});
