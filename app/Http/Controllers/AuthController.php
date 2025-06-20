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

    public function authCallback()
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
        Auth::login($user);
        return ApiResponse::success($user);
    }
}
