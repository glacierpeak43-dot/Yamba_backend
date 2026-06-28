<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\DeviceToken;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Auth\Events\Registered;

class RegisterController extends Controller
{
    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|min:3',
            'email' => 'required|email',
            'university_id' => 'required',
            'password' => 'required|min:5',
            'confirm_password' => 'required|same:password',
            'token' => 'required',
            'referral_code' => 'nullable|string'
        ]);

        $role = Role::where('name', 'user')->first();
        $data['role_id'] = $role->id;

        // Check if the user exists (including soft deleted users)
        $user = User::withTrashed()
            ->where('email', $request->email)
            ->first();

        if (!is_null($user)) {
            if ($user->trashed()) {
                // Check if the user was deleted for more than 30 days
                $deletedDaysAgo = $user->deleted_at->diffInDays(now());
                if ($deletedDaysAgo > 30) {
                    // Remove the old user permanently
                    $user->forceDelete();

                    // Create a new user
                    $newUser = User::create([
                        'name' => $data['name'],
                        'email' => $data['email'],
                        'university_id' => $data['university_id'],
                        'password' => Hash::make($data['password']),
                        'type' => $role->name,
                    ]);

                    $newUser->role()->create([
                        'role_id' => $data['role_id'],
                    ]);

                    $accessToken = $newUser->createToken('authToken')->plainTextToken;

                    // Save deviceToken
                    $deviceToken = $this->saveDeviceToken($newUser, $data['token']);

                    if (!empty($data['referral_code'])) {
                        $this->handleReferral($data['referral_code']);
                    }

                    $newUser->sendEmailVerificationNotification();
                    $name = $newUser->name;

                    $this->sendNotification("Your friend $name has signed in with your code", "Message From Admin", $this->tokens($newUser->id), 'referral', 'notification',);

                    return $this->jsonSuccess(200, "Registered successfully", [
                        'user' => new UserResource($newUser),
                        'access_token' => $accessToken,
                        'device_token' => $deviceToken
                    ], "user");
                } else {
                    // Restore the soft deleted user
                    $user->restore();
                    $user->email_verified_at = null; // Set verification status to null
                    $user->name = $data['name'];
                    $user->university_id = $data['university_id'];
                    $user->password = Hash::make($data['password']);
                    $user->save();
                    $deviceToken = $this->saveDeviceToken($user, $data['token']);
                    $accessToken = $user->createToken('authToken')->plainTextToken;

                    if (!empty($data['referral_code'])) {
                        $this->handleReferral($data['referral_code']);
                    }

                    return $this->jsonSuccess(200, "Registered successfully", [
                        'user' => new UserResource($user),
                        'access_token' => $accessToken,
                        'device_token' => $deviceToken
                    ], "user");
                }
            } else {
                return response()->json(['success' => false, 'message' => 'Sorry! This email is already registered'], 400);
            }
        } else {
            // Create and return data
            $newUser = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'university_id' => $data['university_id'],
                'password' => Hash::make($data['password']),
                'type' => $role->name,
            ]);

            $newUser->role()->create([
                'role_id' => $data['role_id'],
            ]);

            $accessToken = $newUser->createToken('authToken')->plainTextToken;

            // Save deviceToken
            $deviceToken = $this->saveDeviceToken($newUser, $data['token']);

            // Handle referral code
            if (!empty($data['referral_code'])) {
                $this->handleReferral($data['referral_code']);
            }

            $newUser->sendEmailVerificationNotification();

            return $this->jsonSuccess(200, "Registered successfully", [
                'user' => new UserResource($newUser),
                'access_token' => $accessToken,
                'device_token' => $deviceToken
            ], "user");
        }
    }

    //create function to check if user alread exists
    public function checkIfUserExists($email)
    {
        $user = User::where('email', $email)->first();
        !is_null($user) ? true : false;
    }

    public function refer(Request $request){
        $user = User::where('referral_code', '=', $request->ref)->get()->first();
        return view('refer')->with('user', $user);
    }

    public function handleReferral($referralCode)
    {
        $referrer = User::where('referral_code', $referralCode)->first();
        if ($referrer) {
            $referrer->increment('referral_count');
            if ($referrer->referral_count == 50) {
               $this->sendNotification('Congrats! you have reached 50 referrals to claim your price send an email with your profile details to z.monta@unesco.org', "Message From Admin", $this->tokens($referrer->id), 'referral', 'notification');

            }
        }
    }
}
