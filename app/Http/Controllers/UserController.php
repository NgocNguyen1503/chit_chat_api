<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use Auth;
use Illuminate\Http\Request;

class UserController extends Controller
{
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
