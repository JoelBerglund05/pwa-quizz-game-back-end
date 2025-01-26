<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Qusetions;
use App\Models\User;
use App\Models\ActiveGames;
use App\Models\FriendsList;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class GameController extends Controller
{
    //

    private function searchExistingGames(User $user){
        $findExistingGames = ActiveGames::select(
            "id",
            DB::raw("CASE WHEN user_name_1='$user->name' THEN user_name_2 ELSE user_name_1 END as user_name_1"),
            "user_points_1",
            "user_points_2",
            DB::raw("CASE WHEN user_turn='$user->id' THEN user_name_2 ELSE user_name_1 END as user_turn"),
            "rounds"
        )->where("user_id_2", null)->where("user_id_1", "!=", $user->id)->first();

        if(!$findExistingGames){
            return null;
        }

        $updatedGame = $findExistingGames->update(['user_id_2' => $user->id, 'user_name_2' => $user->name]);


        return [
            "game" => $findExistingGames
        ];
    }

    public function getQuestion(Request $request):JsonResponse {
        $request->validate([
            "id"=> "integer",
        ]);

        $user = $request->user();

        $questionsData = Qusetions::inRandomOrder()->limit(3)->get();

        if($questionsData->count() < 3){
            return response()->json([
                "message"=> "Not enaugh questions found",
            ], 404);
        }

        ActiveGames::where("id", $request["id"])->where(function ($query) use ($user){ $query->where("user_id_1", $user->id)->orWhere("user_id_2", $user->id); })->update(["question_1" => $questionsData[0]["question"], "question_2" => $questionsData[1]["question"], "question_3" => $questionsData[2]["question"]]);

        return response()->json([
            "questions" => $questionsData,
        ], 200);
    }

    public function updateActiveGame(Request $request): JsonResponse {
        $user = $request->user();
        $answers = $request->answers;
        $game = ActiveGames::where("id", $request["id"])->where(function ($query) use ($user){ $query->where("user_id_1", $user->id)->orWhere("user_id_2", $user->id); })->first();

        if(!$game){
            return response()->json([
                "message"=> "No game Found!",
            ], 404);
        } else if ($game->user_turn != $user->id){
            return response()->json([
                "message" => "Not your turn!"
            ], 405);
        }

        $questionsGiven = [$game->question_1, $game->question_2, $game->question_3];
        $pointsGiven = 0;

        for($i = 0; $i < count($questionsGiven); $i++){
            $question = Qusetions::where("question", $questionsGiven[$i])->first();

            if($question->answer1 == $answers[$i]){
                $pointsGiven++;
            }
        }

        if ($user->id == $game->user_id_1){
            $oponentId = $game->user_id_2;
        } else {
            $oponentId = $game->user_id_1;
        }


        if($user->name == $game->user_name_1){
            $game->update(['user_turn' => $oponentId, "user_points_1", $game->user_points_1 + $pointsGiven]);
        } else {
            $game->update(['user_turn' => $oponentId, "user_points_2", $game->user_points_2 + $pointsGiven]);
        }

        return response()->json([
            "points_added" => $pointsGiven,
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
            "user_name_1" => $user->name,
            "user_turn" => $user->id,
            "user_points_1" => 0,
            "user_points_2" => 0,
            "rounds" => 0,
        ]);

        return response()->json([
            "game" => $createGame
        ], 200);
    }

    public function getAllMyActiveGames(Request $request){
        $user = $request->user();
        $userGames = ActiveGames::select('id', 'user_name_1', 'user_name_2', 'user_points_1', 'user_points_2', 'user_turn', 'question_1', 'question_2', 'question_3')->where("user_id_1", '=',$user->id)->orWhere("user_id_2",'=', $user->id)->get();

        if (is_null($userGames)){
            return response()->json(["message" => "No active games found!"], 404);
        } else if (count($userGames) == 0){
            return response()->json(["message" => "No active games found!"], 404);
        } else if(!$userGames){
            return response()->json(["message" => "No active games found!"], 404);
        }

        if($user->id == $userGames[0]->user_turn){
            $userGames[0]->user_turn = $user->name;
        } else if ($user->name == $userGames[0]->user_name_1){
            $userGames[0]->user_turn = $userGames[0]->user_name_2;

        } else {
            $userGames[0]->user_turn = $userGames[0]->user_name_1;
        }



        return response()->json($userGames,200);

    }

    public function createGameWithFriend(Request $request){
        $user = $request->user();
        $friend = User::where("email" , $request->friend_email)->first();

        if (!$friend) {
            return response()->json([
                "error" => "No user found with this email"
            ], 404);
        }

        $createGame = ActiveGames::create([
            "user_id_1" => $user->id,
            "user_id_2" => $friend->id,
            "user_name_1" => $user->name,
            "user_name_2" => $friend->name,
            "user_turn" => $user->id,
            "user_points_1" => 0,
            "user_points_2" => 0,
            "rounds" => 0,
        ]);
        return response()->json([
            "game" => [
                "id" => $createGame->id,
                "user_name_1" => $user->name,
                "user_name_2" => $friend->name,
                "user_turn" => $user->name,
                "user_points_1" => 0,
                "user_points_2" => 0,
                "rounds" => 0,
            ]
        ], 200);
    }
}
