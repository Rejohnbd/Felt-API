<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * @OA\Post(
     *     path="/login",
     *     tags={"User Login"},
     *     summary="Logs user into system",
     *     operationId="loginUser",
     *     @OA\Parameter(
     *         name="email",
     *         in="query",
     *         description="The user email for login",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="password",
     *         in="query",
     *         required=true,
     *         @OA\Schema(
     *             type="string",
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="successful operation",
     *         @OA\Header(
     *             header="X-Rate-Limit",
     *             description="calls per hour allowed by the user",
     *             @OA\Schema(
     *                 type="integer",
     *                 format="int32"
     *             )
     *         ),
     *         @OA\Header(
     *             header="X-Expires-After",
     *             description="date in UTC when token expires",
     *             @OA\Schema(
     *                 type="string",
     *                 format="datetime"
     *             )
     *         ),
     *         @OA\JsonContent(
     *             type="string"
     *         ),
     *         @OA\MediaType(
     *             mediaType="application/xml",
     *             @OA\Schema(
     *                 type="string"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid username/password supplied"
     *     )
     * )
     */
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
            $checkEmailExists = User::where('email', $request->email)->exists();

            if ($checkEmailExists) {
                if (Auth::attempt($request->only(['email', 'password']))) {
                    $user = Auth::user()->load(['userRole', 'userDetails']);
                    return response()->json([
                        'status'        => 200,
                        'message'       => 'Logged in successfully',
                        'user'          => Auth::user(),
                        'access_token'  => $user->createToken($user->email)->plainTextToken
                    ]);
                } else {
                    return response()->json([
                        'status'    => false,
                        'message'   => 'Email & Password not Match',
                    ], 401);
                    // throw new AuthenticationException();
                }
            } else {
                return response()->json([
                    'status'    => false,
                    'message'   => 'Email Not Exists.',
                ], 400);
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
        $user = $request->user();
        $user->tokens()->delete();
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return response()->json([
            'status'        => 200,
            'message'       => 'Logout Successfully',
            'user'          => $user
        ]);
    }

    public function allUsers(Request $request)
    {
        if (env('API_SECRET_KEY') == $request->header('api_secret_key')) {
            return 'ok';
            // return User::with(['userRole', 'userDetails'])->get();
        } else {
            return response()->json([
                'status'    => 400,
                'message'   => "it is ok check api",
            ]);
            // dd(env('API_SECRET_KEY'), $request->header('ap_secret_key'));
        }
    }

    public function sanctumAuth(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }
        // $user->load(['userRole', 'userDetails']);

        // return response()->json([
        //     'status'    => true,
        //     'user'      => $user,
        //     'token'     => $user->createToken($request->email)->plainTextToken
        // ]);
        $user->createToken($request->email)->plainTextToken;
    }

    public function sanctumRevoke(Request $request)
    {
        // Need To Test Here
        // $user = Auth::user();
        // return response()->json(['status' => 'Rejohn', 'user' => $user]);
        $user = $request->user();
        $user->tokens()->delete();
        // $user->tokens()->where('id', $tokenId)->delete();
        return 'Delete Token';
    }
}
