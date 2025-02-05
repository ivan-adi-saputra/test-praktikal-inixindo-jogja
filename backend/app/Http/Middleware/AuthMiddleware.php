<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!$token = $request->header("Authorization")) {
            return response()->json([
                'message' => 'Unautorized',
                'code' => 401,
                'error' => 'UNAUTORIZED'
            ], 401);
        }

        # check bearer type
        if (!str_contains($token, 'Bearer')) {
            return response()->json([
                'message' => 'Token type must be bearer',
                'code' => 401,
                'error' => 'UNAUTORIZED'
            ], 401);
        }

        try {
            $payload = JWTAuth::parseToken()->getPayload();
            $user_id = $payload['sub'];
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Token is invalid',
                'code' => 401,
                'error' => $e->getMessage()
            ], 401);
        }

        if (!$user = User::find($user_id)) {
            return response()->json([
                'message' => 'Customer not found',
                'code' => 401,
                'error' => 'UNAUTORIZED'
            ], 401);
        }

        $request->attributes->set('user', $user);

        return $next($request);
    }
}
