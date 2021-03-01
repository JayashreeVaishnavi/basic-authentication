<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Login using JWT
     *
     * @param LoginRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(LoginRequest $request)
    {
        try {
            $credentials = $request->only(['email', 'password']);
            if (!$token = Auth::attempt($credentials)) {
                return response()->json(['error' => 'Unauthorized'], STATUS_CODE_UNAUTHORISED);
            }
            return $this->tokenResponse($token);
        } catch (\Throwable $exception) {
            return response()->json(['error' => 'Something went wrong'], STATUS_CODE_ERROR);
        }
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        return response()->json(auth()->user());
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        try {
            Auth::logout();
            return response()->json(['message' => 'Successfully logged out']);
        } catch (\Throwable $exception) {
            return response()->json(['error' => 'Something went wrong'], STATUS_CODE_ERROR);
        }
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        try {
            return $this->tokenResponse(auth()->refresh());
        } catch (\Throwable $exception) {
            return response()->json(['error' => 'Something went wrong'], STATUS_CODE_ERROR);
        }
    }

    /**
     * Get the token array structure.
     *
     * @param string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function tokenResponse($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
        ]);
    }

    /**
     * @param RegisterRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(RegisterRequest $request)
    {
        DB::beginTransaction();
        try {
            User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);
            DB::commit();
            if (!$token = Auth::attempt(request(['email', 'password']))) {
                return response()->json(['error' => 'Unauthorized'], STATUS_CODE_UNAUTHORISED);
            }
            return $this->tokenResponse($token);
        } catch (\Throwable $exception) {
            info($exception);
            DB::rollBack();
            return response()->json(['error' => 'Something went wrong'], STATUS_CODE_ERROR);
        }
    }
}
