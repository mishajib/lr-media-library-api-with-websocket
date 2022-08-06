<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AuthenticationController extends Controller
{
    /**
     * @param RegisterRequest $request
     * @return JsonResponse
     */
    public function register(RegisterRequest $request)
    {
        $user = User::create($request->validated());

        $token = $user->createToken('authToken')->accessToken;

        return response()->json([
            'success' => true,
            'message' => 'User registered successfully.',
            'user'    => new UserResource($user),
            'token'   => $token
        ], Response::HTTP_CREATED);
    }


    public function login(LoginRequest $request)
    {
        if (!auth()->attempt($request->validated())) {
            return error_response(
                'Invalid credentials!',
                Response::HTTP_UNAUTHORIZED
            );
        }

        $token = auth()->user()->createToken('authToken')->accessToken;

        return success_response(
            'User logged in successfully.',
            Response::HTTP_OK,
            [
                'user'  => new UserResource(auth()->user()),
                'token' => $token
            ]
        );
    }

    public function refreshToken(Request $request)
    {
        $request->user()->token()->refresh();
        $token = $request->user()->createToken('authToken')->accessToken;

        return success_response(
            'Token refreshed successfully.',
            Response::HTTP_OK,
            [
                'token' => $token
            ]
        );
    }

    public function logout(Request $request)
    {
        $request->user()->token()->revoke();
        return success_response(
            'User logged out successfully.',
            Response::HTTP_OK
        );
    }

    public function currentUser(Request $request)
    {
        return success_response(
            'User found!',
            Response::HTTP_OK,
            [
                'user' => new UserResource($request->user())
            ]
        );
    }
}
