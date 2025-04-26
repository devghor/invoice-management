<?php

declare(strict_types=1);

namespace Modules\Auth\Http\Controllers;

use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Base\Enums\StatusCodeEnum;
use Modules\Base\Http\Controllers\BaseController;

final class AuthController extends BaseController
{
    /**
     * Handle user login and return access & refresh tokens.
     */
    public function login(Request $request)
    {
        try {
            // Validate request
            $request->validate([
                'email' => 'required|email',
                'password' => 'required',
            ]);

            // Check credentials
            if (! Auth::attempt($request->only('email', 'password'))) {
                return $this->errorResponse(['error' => 'Credential does not match.'], StatusCodeEnum::UNPROCESSABLE_ENTITY);
            }

            // Get authenticated user
            $user = Auth::user();

            // Delete old tokens
            $user->tokens()->delete();

            // Create new access & refresh tokens
            $accessToken = $user->createToken('access_token', ['*'], now()->addMinutes(15))->plainTextToken;
            $refreshToken = $user->createToken('refresh_token', ['refresh'], now()->addDays(7))->plainTextToken;

            return $this->successResponse([
                'access_token' => $accessToken,
                'refresh_token' => $refreshToken,
                'token_type' => 'Bearer',
                'user' => $user,
            ]);
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), 'Login failed');
        }
    }

    /**
     * Refresh access token using refresh token.
     */
    public function refreshToken(Request $request)
    {
        try {
            $request->validate([
                'refresh_token' => 'required',
            ]);

            // Find user by refresh token
            $user = User::whereHas('tokens', function ($query) use ($request) {
                $query->where('token', hash('sha256', $request->refresh_token))
                    ->where('name', 'refresh_token');
            })->first();

            if (! $user) {
                return $this->errorResponse(['error' => 'User is not found'], StatusCodeEnum::UNPROCESSABLE_ENTITY);
            }

            // Delete old access tokens
            $user->tokens()->where('name', 'access_token')->delete();

            // Create new access token
            $newAccessToken = $user->createToken('access_token', ['*'], now()->addMinutes(15))->plainTextToken;

            return $this->successResponse([
                'access_token' => $newAccessToken,
                'token_type' => 'Bearer',
            ]);
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), 'Refresh token failed');
        }
    }

    /**
     * Logout user and revoke all tokens.
     */
    public function logout(Request $request)
    {
        try {
            $request->user()->tokens()->delete();

            return $this->successResponse('Logged out successfully');
        } catch (Exception $e) {
            return $this->errorResponse('Logout failed', ['error' => $e->getMessage()], StatusCodeEnum::UNPROCESSABLE_ENTITY);
        }
    }

    /**
     * Get authenticated user details.
     */
    public function user(Request $request)
    {
        try {
            return $this->successResponse('User fetched successfully', $request->user());
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), 'Failed to fetch user data');
        }
    }
}
