<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        // return $request->all();
        $validator = validator(
            $request->all(),
            [
                'email'     => 'required|email|max:255',
                'password'  => 'required|string|max:255'
            ],
            [
                'email.required'    => 'Email is Required',
                'email.email'       => 'Provide Valid Email',
                'email.max'         => 'Provide Valid Email',
                'password.required' => 'Password is Required',
                'password.string'   => 'Provide Valid Password',
                'password.max'      => 'Provide Valid Password',
            ]
        );

        if ($validator->fails()) {
            return response()->json([
                'status'    => 400,
                'message'   => $validator->getMessageBag()->first(),
                'errors'    => $validator->getMessageBag()
            ]);
        } else {
            if (Auth::attempt($request->only(['email', 'password']))) {
                $user = Auth::user()->load(['userRole', 'userDetails']);
                return response()->json([
                    'status'        => 200,
                    'message'       => 'Logged in successfully',
                    'user'          => Auth::user(),
                    'access_token'  => $user->createToken($user->email)->plainTextToken
                ]);
            } else {
                throw new AuthenticationException();
            }
        }
    }

    public function user()
    {
        return Auth::user()->load(['userRole', 'userDetails']);
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

    public function allUsers(Request $request)
    {
        if (env('API_SECRET_KEY') == $request->header('api_secret_key')) {
            return 'ok';
            // return User::with(['userRole', 'userDetails'])->get();
        } else {
            return response()->json([
                'status'    => 400,
                'message'   => "not match",
            ]);
            // dd(env('API_SECRET_KEY'), $request->header('ap_secret_key'));
        }
    }
}
