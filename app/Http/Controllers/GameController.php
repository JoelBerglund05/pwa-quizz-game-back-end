<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Questions;
use App\Models\User;
use App\Models\ActiveGames;
use Illuminate\Http\JsonResponse;



class GameController extends Controller
{
    //

    private function searchExistingGames(User $user){
        $findExistingGames = ActiveGames::where("user_id_2", null)->get();

        for($i = 0; $i < count($findExistingGames); $i++){
            if($user->id != $findExistingGames[$i]["user_id_1"]){
                $findExistingGames[$i]["user_id_2"] = $user->id;
                $findExistingGames->save();
                return response()->json([
                    "game" => $findExistingGames[$i]["i"],
                ]);
            }
        }
    }

    public function getQuestion():JsonResponse {
        $questionsData = Questions::inRandomOrder()->limit(3)->get();
        $questions = [];
        for($i = 0; $i < count($questionsData); $i++){
            array_push($questions, $questionsData[$i]["question"]);
        }
        return response()->json([
            "questions" => $questions,
        ], 200);
    }

    public function createGame(Request $request):JsonResponse{
        $fields = $request->validate([
            "email" => "required|string"
        ]);

        $user = User::where("email", $request["email"])->first();

        $searchForGames = $this->searchExistingGames($user);

        if ($searchForGames != null) {
            return response()->json($searchForGames,200);
        }

        $createGame = ActiveGames::create([
            "user_id_1" => $user->id,
            "user_turn" => $user->id,
            "user_points_1" => 0,
            "user_points_2" => 0,
        ]);

        return response()->json([
            "game" => $createGame
        ], 200);
    }
}
