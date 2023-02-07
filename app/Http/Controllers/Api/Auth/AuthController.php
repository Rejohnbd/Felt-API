<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        if (!Auth::attempt($request->only(['email', 'password']))) {
            throw new AuthenticationException();
        } else {
            $user = Auth::user();
            return response()->json([
                'status'        => 200,
                'message'       => 'Logged in successfully',
                'user'          => Auth::user(),
                'access_token'  => $user->createToken($user->email)->plainTextToken
            ]);
        }
    }

    public function user()
    {
        return Auth::user();
        // $accessToken = $user->createToken($user->name)->plainTextToken;
        // return response()->json([
        //     'status'        => 200,
        //     'message'       => 'Logged in successfully',
        //     'user'          => $user,
        //     'access_token'  => $accessToken
        // ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        // return response()->json([
        //     'status'        => 200,
        //     'message'       => 'Logout Successfully'
        // ]);
    }
}
