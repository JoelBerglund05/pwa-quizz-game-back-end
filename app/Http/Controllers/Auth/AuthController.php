<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;

class AuthController extends Controller
{
    public function redirectToProvider($provider){
        $validated = $this->validatedProvider($provider);
        if(!is_null($validated)){
            return $validated;
        }

        return Socialite::driver($provider)->stateless()->redirect();
    }

    public function handleProviderCallback($provider){
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

        $token = $userCreated->createToken('user-token')->plainTextToken;

        return response()->json(['user' => $userCreated,'Access-Token' => $token], 200);
    }

    protected function validatedProvider($provider){
        if(!in_array($provider, ['google'])){
            return response()->json(['error' => 'Invalid provider.'], 422);
        }
    }
}
