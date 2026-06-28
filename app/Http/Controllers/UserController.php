<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserSearchResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    function getCounsellors(Request $request)
    {
        $counsellors = User::where('type', 'counsellor')->where('university_id' ,'=', Auth::user()->university_id)->where('id', '!=', Auth::user()->id)->get();

        return $this->jsonSuccess(200, "Counsellors fetched successfully", UserSearchResource::collection($counsellors), "users");
    }
    function getCounsellor(Request $request, User $user)
    {
        return $this->jsonSuccess(200, "Counsellors fetched successfully", new UserSearchResource($user), "user");
    }
}
