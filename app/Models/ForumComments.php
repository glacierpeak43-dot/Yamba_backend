<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ForumComments extends Model
{
    use HasFactory;
    protected $guarded;

    protected $with = ['votes', 'replies'];

    public function replies()
    {
        return $this->hasMany(ForumCommentReply::class,'forum_comment_id');
    }

    public function votes()
    {
        return  $this->hasMany(ForumCommentVote::class, 'forum_comment_id');
    }


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    //create function to check if user already voted on comment
    public function userVoted($user_id)
    {
        return $this->votes()->where('user_id', $user_id)->exists();
    }
}
