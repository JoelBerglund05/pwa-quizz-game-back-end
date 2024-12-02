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
                ActiveGames::where('user_id_1',$findExistingGames[$i]['user_id_1'])->update(['user_id_2' => $user->id]);
                return [
                    "game" => $findExistingGames[$i]["id"],
                ];
            }
        }
    }

    private function getCorectGame(int $id, User $user){
        $findExistingGames = ActiveGames::where("id", $id)->get();

        if ($findExistingGames[0]["user_id_1"] == $user->id || $findExistingGames[0]["user_id_2"] == $user->id){
            return $findExistingGames;
        }
    }

    public function getQuestion(Request $request):JsonResponse {
        $request->validate([
            "id"=> "integer",
        ]);

        $user = $request->user();

        $game = $this->getCorectGame($request["id"], $user);

        $questionsData = Questions::inRandomOrder()->limit(3)->get();
        $questions = [];
        for($i = 0; $i < count($questionsData); $i++){
            array_push($questions, $questionsData[$i]["question"]);
        }

        ActiveGames::where("id", $game[0]["id"])->update(["question_1" => $questions[0], "question_2" => $questions[1], "question_3" => $questions[2],]);

        return response()->json([
            "questions" => $questions,
        ], 200);
    }

    public function createGame(Request $request):JsonResponse{
        $user = $request->user();

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

    public function getAllMyActiveGames(Request $request){
        $user = $request->user();
        $userGames = ActiveGames::where(["user_id_1", "user_id_2"], $user["id"])->get();


    }
}
