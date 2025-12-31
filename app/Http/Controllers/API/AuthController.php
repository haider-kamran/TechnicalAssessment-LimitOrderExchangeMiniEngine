<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{

    /**
     * Login user and create token
     */
    public function login(Request $request)
    {
        try {
            $credentials = $request->validate([
                'email' => ['required', 'email'],
                'password' => ['required'],
            ]);

            if (!Auth::attempt($credentials)) {
                return $this->error('The provided credentials are incorrect.', 401);
            }

            $user = $request->user();

            $token = $user->createToken('api_token')->plainTextToken;

            return $this->success('Login successful', [
                'user' => $user,
                'token' => $token,
            ]);
        } catch (\Throwable $e) {
            return $this->error($e->getMessage(), 500);
        }
    }

    /**
     * Logout user (revoke tokens)
     */
    public function logout(Request $request)
    {
        try {
            $request->user()->tokens()->delete();

            return $this->success('Logged out successfully');
        } catch (\Throwable $e) {
            return $this->error($e->getMessage(), 500);
        }
    }

    /**
     * Get authenticated user
     */
    public function me(Request $request)
    {
        try {
            return $this->success('Authenticated user retrieved', [
                'user' => $request->user(),
            ]);
        } catch (\Throwable $e) {
            return $this->error($e->getMessage(), 500);
        }
    }
}
