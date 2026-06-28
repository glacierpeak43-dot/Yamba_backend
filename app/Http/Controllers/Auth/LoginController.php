<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\DeviceToken;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        $data = $request->validate([
            'token' => 'required'
        ]);

        $loginData = $request->validate([
            'email' => 'email|required',
            'password' => 'required',
        ]);

        // Check if the user exists
        $user = User::withTrashed()
            ->where('email', $loginData['email'])
            ->first();

        if (!$user) {
            return $this->jsonError(401, "Invalid credentials");
        }

        // Check if the user is soft deleted and within 30 days
        if ($user->trashed() && $user->deleted_at >= Carbon::now()->subDays(30)) {
            $user->restore();
        }

        if (!Auth::attempt($loginData)) {
            return $this->jsonError(401, "Invalid credentials");
        }

        // Revoke all user tokens
        auth()->user()->tokens()->delete();
        auth()->user()->deviceTokens()->delete();

        // Save deviceToken
        $this->saveDeviceToken(Auth::user(), $data['token']);

        $accessToken = auth()->user()->createToken('authToken')->plainTextToken;
        return $this->jsonSuccess(200, "Login Successful", [
            'user' => new UserResource(auth()->user()),
            'access_token' => $accessToken
        ], "user");
    }

    public function Logout()
    {
        $user = Auth::user();
        DeviceToken::where('user_id', '=', $user->id)->delete();
        $user->currentAccessToken()->delete();

        return response(['success' => true]);
    }

    public function refreshUser(){
        $accessToken = auth()->user()->createToken('authToken')->plainTextToken;
        return $this->jsonSuccess(200, "Login Successful", [
            'user' => new UserResource(auth()->user()),
            'access_token' => $accessToken
        ], "user");

    }
}
