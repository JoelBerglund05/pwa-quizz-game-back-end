<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;


class AuthController extends Controller
{
    public function redirectToProvider($provider){
        $validated = $this->validatedProvider($provider);
        if(!is_null($validated)){
            return $validated;
        }

        return Socialite::driver($provider)->stateless()->redirect();
    }

    public function handleProviderCallback($provider):JsonResponse{
        $validated = $this->validatedProvider($provider);
        if(!is_null($validated)){
            return $validated;
        }

        try {
            $user = Socialite::driver($provider)->stateless()->user();
        } catch (ClientException $exception) {
            return response()->json(['error' => 'Invalid credentials provided.']);
        }

        $userCreated = User::firstOrCreate(
            [
                'email' => $user->getEmail()
            ],
            [
                'email_verified_at' => now(),
                'name' => $user->getName(),
                'status' => true,
            ]
        );
        $userCreated->providers()->updateOrCreate(
            [
                'provider' => $provider,
                'provider_id' => $user->getId()
            ],
            [
                'avatar' => $user->getAvatar(),
            ]
        );

        $token = JWTAuth::fromUser($userCreated);

        return response()->json(compact('user','token'), 201);
    }

    protected function validatedProvider($provider):JsonResponse|null{
        if(!in_array($provider, ['google'])){
            return response()->json(['error' => 'Invalid provider.'], 422);
        }
        return null;
    }
}
