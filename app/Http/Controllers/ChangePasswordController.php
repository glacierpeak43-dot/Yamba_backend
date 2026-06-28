<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ChangePasswordController extends Controller
{
    public function changePassword(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|string|min:6',
        ]);

        if (!(Hash::check($request->get('current_password'), Auth::user()->password))) {
            return response()->json([
                'success' => false,
                'message' => 'Your current password does not match with the password you provided. Please try again.',
            ]);
        }

        if (strcmp($request->get('current_password'), $request->get('new_password')) == 0) {
            //Current password and new password are same
            return response()->json([
                'success' => false,
                'message' => 'New Password cannot be same as your current password. Please choose a different password.',
            ]);
        }

        $user = Auth::user();
        $user->password = bcrypt($request->get('new_password'));
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Password Changed Successfully!',
        ]);
    }
}
