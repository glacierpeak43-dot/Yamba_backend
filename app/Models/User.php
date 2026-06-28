<?php

namespace App\Models;

use App\Notifications\MailResetPasswordNotification;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class   User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;


    protected $dates = ['deleted_at'];
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_blocked_from_system',
        'university_id',
        'has_agreed_forum'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    protected $with = ['profile'];

    //create function to create profile on saving user
    protected static function boot()
    {
        parent::boot();
        static::created(function ($user) {
            $user->profile()->create([
                'user_id' => $user->id,
            ]);
        });
    }

    public function restore()
    {
        $this->deleted_at = null;
        $this->save();
    }

    public function deviceTokens(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(DeviceToken::class);
    }

    //create function tochec if user has role
    public function hasRole($role)
    {
        return $this->role->role->name == $role;
    }

    public function profile()
    {
        return $this->hasOne(UserProfile::class, 'user_id');
    }

    public function role()
    {
        return $this->hasOne(UserRole::class, 'user_id');
    }
    public function university()
    {
        return $this->belongsTo(University::class);
    }


    public function friends()
    {
        return $this->belongsToMany(User::class, 'friend_requests', 'from_user_id', 'to_user_id')->wherePivot('accepted', true);
    }

    public function isFriend($user_id, $user_not_logged)
    {
        $request  = FriendRequests::where('to_user_id', $user_not_logged)
            ->where('from_user_id', $user_id)
            ->orWhere('to_user_id', $user_id)
            ->where('from_user_id', $user_not_logged)->first();
        if ($request) {
            return $request->accepted == 0 ? false : true;
        } else {
            return false;
        }
    }

    public function friendRequestSent($user_id)
    {
        return $this->belongsToMany(User::class, 'friend_requests', 'from_user_id', 'to_user_id')->wherePivot('accepted', false);
    }

    public function friendRequestAccepted($user_id)
    {
        return $this->friendRequestSent($user_id)->wherePivot('accepted', true)->exists();
    }

    public function friendRequestRejected($user_id)
    {
        return $this->friendRequestSent($user_id)->wherePivot('accepted', false)->exists();
    }

    // public function friendRequestAreadySent($user_id)
    // {
    //     return $this->friendRequestSent($user_id)->exists();
    // }

    public function friendRequestAreadySent($user_id, $user_not_logged)
    {
        return FriendRequests::where('to_user_id', $user_not_logged)
            ->where('from_user_id', $user_id)->exists();
    }
    public function friendRequestAreadyReceived($user_id, $user_not_logged)
    {
        // dd($user_id . ' ' . $user_not_logged);
        return FriendRequests::where('to_user_id', $user_id)
            ->where('from_user_id', $user_not_logged)->exists();
    }

    public function friendRequest($user_id)
    {
        return FriendRequests::where('to_user_id', $user_id)->where('from_user_id', $this->id)->orWhere('to_user_id', $this->id)->where('from_user_id', $user_id)->first();
    }

    //get friend request with pivot data
    public function getFriendRequest($user_id)
    {
        $pivot = $this->friendRequest($user_id);
        return FriendRequests::where('from_user_id', $pivot->from_user_id)->where('to_user_id', $pivot->to_user_id)->first();
    }
    //create function to get friend request


    //get chat with user
    public function getChatWithUser($user_id)
    {
        return Chat::where('user_1', $this->id)->where('user_2', $user_id)->orWhere('user_2', $this->id)->where('user_1', $user_id)->first();
    }

    public function myAppointments()
    {
        return Appointment::where('consellor_id', $this->id)->where('status', '!=', 'Cancelled')->orderBy('created_at', 'DESC')->get();
    }

    public function counsellorAppointments()
    {
        return $this->belongsToMany(Appointment::class);
    }

    public function appointments()
    {
        return  $this->counsellorAppointments();
    }

    public function  location()
    {
        return $this->hasOne(Location::class);
    }



    //create function to get friend requests
    public function getFriendRequests()
    {
        return FriendRequests::where('to_user_id', $this->id)->where('accepted', false)->get();
    }
    public function getSentRequest()
    {
        return FriendRequests::where('from_user_id', $this->id)->where('accepted', false)->get();
    }

    //create function to get appointments with counsellor
    public function getAppointmentsWithCounsellor($conselllor_id)
    {
        return Appointment::where('user_id', $this->id)->where('consellor_id', $conselllor_id)->orderBy('created_at', 'DESC')->get();
    }

    public function abuseReports()
    {
        return $this->hasMany(AbuseReports::class);
    }


    public function articleCommentReports()
    {
        return $this->hasMany(ArticleCommentReport::class);
    }
    public function articleCommentReplyReports()
    {
        return $this->hasMany(ArticleCommentReplyReport::class);
    }
    public function forumCommentReports()
    {
        return $this->hasMany(ForumCommentReport::class);
    }
    public function forumCommentReplyReports()
    {
        return $this->hasMany(ForumCommentReplyReport::class);
    }

    //create function determine next level
    // public function determineNextLevel()
    // {
    //     $level = $this->profile->level;
    //     $points = $this->profile->points;
    //     $next_level = Level::where('level', $level + 1)->first();
    //     if ($next_level) {
    //         if ($points >= $next_level->points) {
    //             $this->profile->level = $level + 1;
    //             $this->profile->save();
    //         }
    //     }
    // }
}
