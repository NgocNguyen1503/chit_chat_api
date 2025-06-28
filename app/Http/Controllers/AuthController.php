<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    public function authRedirect()
    {
        $data = collect([
            'url' => Socialite::driver('google')->stateless()->redirect()->getTargetUrl()
        ]);
        return ApiResponse::success($data);
    }

    public function authCallback(Request $request)
    {
        $googleUser = Socialite::driver('google')->stateless()->user();
        $user = User::firstOrCreate([
            'email' => $googleUser->email
        ], [
            'name' => $googleUser->name,
            'email' => $googleUser->email,
            'avatar' => $googleUser->avatar,
            'email_verified_at' => now(),
            'password' => Hash::make('12345678'),
            'online_status' => 1,
        ]);
        $user->token = $user->createToken($user->email)->plainTextToken;
        return redirect()->away("http://localhost:5173/auth/callback?token=$user->token");
    }

    public function login(Request $request)
    {
        $params = $request->all();
        try {
            Auth::attempt([
                'email' => $params['email'],
                'password' => $params['password']
            ]);
            $user = Auth::user();
            $user->token = $user->createToken($user->email)->plainTextToken;
            return ApiResponse::success($user);
        } catch (\Throwable $th) {
            return ApiResponse::dataNotfound();
        }
    }
}
