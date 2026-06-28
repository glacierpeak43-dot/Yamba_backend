<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\DeviceToken;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

class VerificationController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'token' => 'required'
        ]);
        $loginData = $request->validate([
            'email' => 'email|required',
            'password' => 'required',
        ]);
        if (!auth()->attempt($loginData)) {
            return $this->jsonError(401, "Invalid credentials");
        }
        //save deviceToken
        $deviceToken = new DeviceToken;
        $deviceToken->user_id = auth()->user()->id;
        $deviceToken->device_token = $request->input('token');
        $deviceToken->save();

        $accessToken = auth()->user()->createToken('authToken')->plainTextToken;
        return response(['user' => auth()->user(), 'token' => $accessToken, 'success' => true, 'message' => 'Logged in successfully']);
    }

    public function Logout()
    {
        $user = Auth::user();
        DeviceToken::where('user_id', '=', $user->id)->delete();
        $user->currentAccessToken()->delete();

        return response(['success' => true]);
    }

    public function emailVerify($user_id, Request $request)
    {
        if (!$request->hasValidSignature()) {
            return response()->json(["msg" => "Invalid/Expired url provided."], 401);
        }

        $user = User::findOrFail($user_id);

        if (!$user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
        }

        return redirect()->to('/email/success');
    }
    //create function to resend email verification
    public function resendEmailVerification(Request $request)
    {
        $request->user()->sendEmailVerificationNotification();
        return response()->json(['success' => true, "message" => "Email verification sent successfully"]);

    }

    public function verificationEmailSuccess()
    {
        return view('auth.verification-email-success');
    }
}
