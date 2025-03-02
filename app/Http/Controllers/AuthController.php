<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Services\AuthService;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    protected AuthService $authService;
    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function register(Request $request): Response
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|confirmed|min:8|max:255'
        ]);

        $user = $this->authService->register($request);

        $token = $user->createToken('auth_token')->plainTextToken;
        return response([
            'message' => 'Registered Successfully!\nPlease validate your account with OTP',
            'resutls' => [
                'user' => new UserResource($user),
                'token' => $token
            ]
        ], 201);
    }

    public function login(Request $request): Response
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|confirmed|min:8|max:255'
        ]);

        $user = $this->authService->login($request);

        if (!$user) {
            return response([
                'message' => 'Error with the login credentials...'
            ], 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;
        return response([
            'message' => 'Logged In Successfully!' . ($user->email_verified_at ? '' : '/nPlease validate your account with OTP'),
            'resutls' => [
                'user' => new UserResource($user),
                'token' => $token
            ]
        ]);
    }
    public function otp(Request $request): Response
    {

        $user = Auth::user();

        $otp = $this->authService->otp($user);

        return response([
            'message' => 'Verfication Code sent successfully!/nPlease check your email.',

        ]);
    }
    public function verify(Request $request): Response
    {
        $request->validate([
            'code' => 'required|numeric',
        ]);

        $user = Auth::user();

        $otp = $this->authService->verify($user, $request);

        return response([
            'message' => 'Verfication Success!',
            'resutls' => [
                'user' => new UserResource($user),
            ]
        ]);
    }

    public function resetOtp(Request $request): Response
    {
        $valid=$request->validate(
            [
                'email' => 'required|email|max:255|exists:users,email'
            ]
        );
 
        $user = $this->authService->getUserByEmail($request->email);

        $this->authService->otp($user, 'password-reset');

        return response([
            'message' => 'Password Reset Code has been sent to your email'
        ]);
    }

    public function resetPassword(Request $request): Response
    {
        $request->validate(
            [
                'email' => 'required|email|max:255|exists:users,email',
                'password' => 'required|confirmed|min:8|max:255'
            ]
        );

        $user = $this->authService->getUserByEmail($request->email);

        $this->authService->resetPassword($user, $request);

        return response([
            'message' => 'Password has been reset successfully!'
        ]);
    }
}
