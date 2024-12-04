<?php

namespace App\Http\Controllers;

use App\Models\FriendsList;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FriendsController extends Controller
{
    //

    public function addFriend(Request $request){
        $user = $request->user();

        $friend = User::where("email", $request->friend_email)->first();

        if(!$friend){
            return response()->json([
                "message" => "No user with that eamil found"
            ], 404);
        }

        $friendShip = FriendsList::create([
            "email_1" => $user->email,
            "email_2" => $friend->email,
            "name_1" => $user->name,
            "name_2" => $friend->name
        ]);

        return response()->json([
            "message" => "Friend Added!",
            "friend_ship" => $friendShip
        ], 200);
    }

    public function getFriends(Request $request){
        $user = $request->user();

        $usersFriends = FriendsList::select(DB::raw("(CASE WHEN name_1 = '$user->name' THEN name_2 ELSE name_1 END) as name"), DB::raw("(CASE WHEN name_1 = '$user->name' THEN email_2 ELSE email_1 END) as email"))->where("email_1", $user->email)->orWhere("email_2", $user->email)->get();

        if(!$usersFriends){
            return response()->json([
                "message"=> "No friends found"
            ],200);
        }

        return response()->json([
            "friends" => $usersFriends
        ], 200);
    }
}
