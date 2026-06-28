<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\ProfileArticleView;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class UserProfileController extends Controller
{
    public function store(Request $request)
    {

        $data =  $request->validate([
            'image' => 'required|image:jpg:jpeg:png',
            'phone_number' => 'required',
            'nick_name' => 'required',
            'bio' => 'required',
        ]);
        //do everything here
        $user = Auth::user();
        if ($user->profile) {
            return $this->jsonError(400, "You already have a profile");
        }
        if ($request->has('image')) {
            $image = $data['image'];
            $filenameWithExt = $image->getClientOriginalName();
            $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
            $extension = $image->getClientOriginalExtension();
            $token = Str::random(32);
            $filenameToStore = $filename . "$token" . '_.' . $extension;
            $image->storeAs('public/user_profile_images/', $filenameToStore);
        }
        $userProfile = UserProfile::create([
            'user_id' => $user->id,
            'image_path' => $filenameToStore,
            'phone_number' => $data['phone_number'],
            'nick_name' => $data['nick_name'],
            'bio' => $data['bio'],
        ]);

        $user->save();
        return $this->jsonSuccess(200, "Profile created successfully", [
            'user' => new UserResource(User::find($user->id)),
        ], "user");
    }

    public function delete()
    {
        $user = Auth::user();
        $user_id = $user->id;
        $profile = UserProfile::where('user_id', '=', $user_id)->first();
        if (!$profile) {
            return $this->jsonError(400, "You don't have a profile");
        }
        \Storage::delete("public/user_profile_images/" . $profile->image_path);
        $profile->delete();
        return $this->jsonSuccess(200, "Profile deleted successfully", [
            'user' => new UserResource(User::find($user_id)),
        ], "user");
    }

    //create functioon to show profile
    public function show()
    {
        $user = Auth::user();
        $user_id = $user->id;
        return $this->jsonSuccess(200, "Profile found", [
            'user' => new UserResource($user),
        ], "profile");
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        //do everything here
        $filenameToStore = '';
        if ($request->has('image')) {
            $image = $request->image;
            $filenameWithExt = $image->getClientOriginalName();
            $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
            $extension = $image->getClientOriginalExtension();
            $token = Str::random(32);
            $filenameToStore = $filename . "$token" . '_.' . $extension;
            $image->storeAs('public/user_profile_images/', $filenameToStore);
        }
        $user->name = $request->input('name');

        $user->profile->update([
            'image_path' => $filenameToStore,
            'phone_number' => $request->input('phone_number'),
            'nick_name' => $request->input('nick_name'),
            'bio' => $request->input('bio'),
        ]);
        $user->profile->save();
        return $this->jsonSuccess(200, "Profile Updated Successfully", [
            'user' => new UserResource($user),
        ], "user");
    }

    //create function to check if user aready have profile
    public function checkProfile(User $user)
    {
        $user->profile ? true : false;
    }

    public function updateArticleViews(Request $request)
    {
        $data = $request->validate([
            'article_id' => 'required',
        ]);

        $profile = Auth::user()->profile;
        //article aready viewd
        if ($profile->isArticleViewed($data['article_id'])) {
            return $this->jsonError(200, "Article already viewed");
        }
        $profile->articleViews()->create([
            'article_id' => $data['article_id'],
        ]);
        //update profile level
        $islevelUp = $profile->updateLevel();
        return $this->jsonSuccess(200, "Article viewed successfully", [
            'user' => new UserResource(Auth::user()),
            'isLevelUp' => $islevelUp,
        ], "profile");
    }

    public function acceptForumTermsAndConditions()
    {
        $user = Auth::user();
        if (!Auth::user()->has_agreed_forum) {
            $user->update([
                'has_agreed_forum' => true
            ]);
            return response(['success' => true, 'message' => 'accepted terms and conditions',]);
        } else {
            return response(['success' => false, 'message' => 'already accepted terms and conditions',]);
        }
    }
}
