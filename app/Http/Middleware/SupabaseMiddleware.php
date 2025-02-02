<?php

namespace App\Http\Middleware;

use App\Models\Profiles;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Http;


class SupabaseMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $auth = $request->header('Authorization');
        $apiKey = $request->header('Apikey');
        $response = Http::withHeaders([
            'Apikey' => $apiKey,
            'authorization' => "Bearer $auth"
        ])->get('https://quchkaleqfbxkufbskck.supabase.co/auth/v1/user');

        if ($response->clientError()) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }




            $profile = Profiles::where("user_id", $response["id"])->first();

            if (is_null($profile)) {
                $profile = Profiles::create([
                    "display_name" => "temp",
                    "user_id" => $response["id"]
                ]);
            }


        $userData = ["uid" => $response["id"], "email" =>  $response["email"], "display_name" => $profile["display_name"]];


        $request->merge($userData);



        return $next($request);
    }
}
