<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Questions;
use App\Models\User;
use App\Models\ActiveGames;
use App\Models\FriendsList;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class GameController extends Controller
{
    //
    public function setQuestion(Request $request){
        Questions::create([
            "question" => $request->question,
            "answer1" => $request->answer1,
            "answer2"=> $request->answer2,
            "answer3"=> $request->answer3,
            "answer4" => $request->answer4,
            "category" => $request->category
        ]);
        $games = Questions::select("id")->where("category", $request->category)->get();
        return response()->json([
            "message" => "Question created!",
            "game"=> $games
        ], 200);
    }
    public function getQuestion(Request $request):JsonResponse {


        $questionsData = Questions::inRandomOrder()->where("category", $request->category)->limit(10)->get();

        if($questionsData->count() < 10){
            return response()->json([
                "message"=> "Not enaugh questions found",
            ], 404);
        }

        return response()->json([
            "questions" => $questionsData,
        ], 200);
    }

}
