<?php

namespace App\Http\Controllers;

use App\Models\Profiles;
use Illuminate\Http\Request;
use App\Models\Questions;
use App\Models\ActiveGames;
use App\Models\FriendsList;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use function Sodium\increment;

class GameController extends Controller
{
    //

    private function searchExistingGames(array $user){
        $findExistingGames = ActiveGames::select(
            "id",
            DB::raw("CASE WHEN user_name_1='{$user["display_name"]}' THEN user_name_2 ELSE user_name_1 END as user_name_1"),
            "user_points_1",
            "user_points_2",
            DB::raw("CASE WHEN user_turn='{$user["uid"]}' THEN user_name_2 ELSE user_name_1 END as user_turn"),
            "rounds"
        )->where("user_id_2", null)->where("user_id_1", "!=", $user["uid"])->first();

        if(!$findExistingGames){
            return null;
        }

        $updatedGame = $findExistingGames->update(['user_id_2' => $user["uid"], 'user_name_2' => $user["display_name"]]);

        return [
            "game" => $findExistingGames
        ];
    }

    public function getQuestion(Request $request):JsonResponse {
        $request->validate([
            "id"=> "integer",
        ]);

        $user = ["email" => $request->email, "display_name" => $request->display_name, "uid" => $request->uid];

        $questionsData = Questions::inRandomOrder()->limit(3)->get();

        if($questionsData->count() < 3){
            return response()->json([
                "message"=> "Not enough questions found",
            ], 404);
        }

        $updateResult = ActiveGames::where("id", $request["id"])->where(function ($query) use ($user){ $query->where("user_id_1", $user["uid"])->orWhere("user_id_2", $user["uid"]); })->update(["question_1" => $questionsData[0]["question"], "question_2" => $questionsData[1]["question"], "question_3" => $questionsData[2]["question"], "user_1_has_answered_question" => false, "user_2_has_answered_question" => false]);

        return response()->json([
            "questions" => $questionsData,
        ], 200);
    }

    public function updateActiveGame(Request $request): JsonResponse {
        $user = ["email" => $request->email, "display_name" => $request->display_name, "uid" => $request->uid];
        $answers = $request->answers;
        $game = ActiveGames::where("id", $request["id"])
            ->where(
                function ($query) use ($user){
                    $query->where("user_id_1", $user["uid"])->orWhere("user_id_2", $user["uid"]);
                })
            ->first();

        if(!$game){
            return response()->json([
                "message"=> "No game Found!"
            ], 404);
        } else if ($game->user_turn != $user["uid"]){
            return response()->json([
                "message" => "Not your turn!"
            ], 405);
        }

        $questionsGiven = [$game->question_1, $game->question_2, $game->question_3];
        $pointsGiven = 0;

        for($i = 0; $i < count($questionsGiven); $i++){
            $question = Questions::where("question", $questionsGiven[$i])->first();

            if($question->answer1 == $answers[$i]){
                $pointsGiven++;
            }
        }

        if ($user["uid"] == $game->user_id_1){
            $oponentId = $game->user_id_2;
        } else {
            $oponentId = $game->user_id_1;
        }


        if($user["uid"] == $game->user_id_1){
            $game->update(['user_turn' => $oponentId, "user_points_1" => $game->user_points_1 + $pointsGiven, 'user_1_has_answered_question' => true]);
        } else {
            $game->update(['user_turn' => $oponentId, "user_points_2" => $game->user_points_2 + $pointsGiven, 'user_2_has_answered_question' => true]);
        }

        if ($game->user_1_has_answered_question == true && $game->user_2_has_answered_question == true){
            $this->getQuestion($request);
        }

        return response()->json([
            "points_added" => $pointsGiven,
        ], 200);
    }

    public function createGame(Request $request):JsonResponse{
        $user = ["email" => $request->email, "display_name" => $request->display_name, "uid" => $request->uid];

        $searchForGames = $this->searchExistingGames($user);

        if ($searchForGames != null) {
            return response()->json($searchForGames,200);
        }

        $createGame = ActiveGames::create([
            "user_id_1" => $user["uid"],
            "user_name_1" => $user["display_name"],
            "user_turn" => $user["uid"],
            "user_points_1" => 0,
            "user_points_2" => 0,
            "rounds" => 0,
        ]);

        return response()->json([
            "game" => $createGame
        ], 200);
    }

    public function getAllMyActiveGames(Request $request){
        $user = ["email" => $request->email, "display_name" => $request->display_name, "uid" => $request->uid];
        $userGames = ActiveGames::select('id', 'user_name_1', 'user_name_2', 'user_points_1', 'user_points_2', 'user_turn', 'question_1', 'question_2',
            'question_3', 'user_1_has_answered_question', 'user_2_has_answered_question')
            ->where("user_id_1", '=', $user["uid"])->orWhere("user_id_2",'=', $user["uid"])
            ->get();

        if (is_null($userGames)){
            return response()->json(["message" => "No active games found!"], 404);
        } else if (count($userGames) == 0){
            return response()->json(["message" => "No active games found!"], 404);
        } else if(!$userGames){
            return response()->json(["message" => "No active games found!"], 404);
        }

        $questions = [Questions::where('question', $userGames[0]["question_1"])
                ->orWhere('question', $userGames[0]["question_2"])
                ->orWhere('question', $userGames[0]["question_3"])
                ->get(),
            Questions::where('question', $userGames[1]["question_1"])
                ->orWhere('question', $userGames[1]["question_2"])
                ->orWhere('question', $userGames[1]["question_3"])
                ->get(),
            Questions::where('question', $userGames[2]["question_1"])
                ->orWhere('question', $userGames[2]["question_2"])
                ->orWhere('question', $userGames[2]["question_3"])
                ->get(),
        ];

        for ($i = 0; $i < count($userGames); $i++){
            if($user["uid"] == $userGames[$i]->user_turn){
                $userGames[$i]->user_turn = $user["display_name"];
            } else if ($user["display_name"] == $userGames[$i]->user_name_1){
                $userGames[$i]->user_turn = $userGames[$i]->user_name_2;

            } else {
                $userGames[$i]->user_turn = $userGames[$i]->user_name_1;
            }
        }



        return response()->json(["games" => $userGames, "questions" => $questions],200);
    }

    public function createGameWithFriend(Request $request){
        $user = ["email" => $request->email, "display_name" => $request->display_name, "uid" => $request->uid];
        $friend = User::where("email" , $request->friend_email)->first();

        if (!$friend) {
            return response()->json([
                "error" => "No user found with this email"
            ], 404);
        }

        $friendProfile = Profiles::where("user_id" , $friend->id)->first();

        if (!$friendProfile) {
            return response()->json([
                "error" => "No user found with this id"
            ]);
        }

        $createGame = ActiveGames::create([
            "user_id_1" => $user["uid"],
            "user_id_2" => $friend->id,
            "user_name_1" => $user["display_name"],
            "user_name_2" => $friendProfile->display_name,
            "user_turn" => $user["uid"],
            "user_points_1" => 0,
            "user_points_2" => 0,
            "rounds" => 0,
        ]);
        return response()->json([
            "game" => [
                "id" => $createGame->id,
                "user_name_1" => $user["display_name"],
                "user_name_2" => $friendProfile->display_name,
                "user_turn" => $user["display_name"],
                "user_points_1" => 0,
                "user_points_2" => 0,
                "rounds" => 0,
            ]
        ], 200);
    }
}
