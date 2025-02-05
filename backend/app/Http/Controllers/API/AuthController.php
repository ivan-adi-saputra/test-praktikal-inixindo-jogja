<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function signin(Request $request)
    {
        try {

            if (!User::where('email', $request->email)->first()) {
                return response()->json([
                    'message' => 'User not found',
                    'code' => 404,
                ], 404);
            }

            if (!$token = Auth::guard('api')->attempt($request->only(['email', 'password']))) {
                return response()->json([
                    'message' => 'Email or password is wrong',
                    '403'
                ], 403);
            }

            return response()->json([
                'message' => 'Sign in successfully',
                'code' => 200,
                'data' => [
                    'access_token' => $token,
                    'token_type' => 'Bearer',
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Sign In Failed',
                'code' => 500,
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
