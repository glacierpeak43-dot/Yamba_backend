<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArticleComment extends Model
{
    use HasFactory;
    protected $guarded;

    protected $with = ['votes', 'replies'];

    public function replies()
    {
        return $this->hasMany(ArticleCommentReply::class);
    }

    public function votes()
    {
        return  $this->hasMany(ArticleCommentVote::class);
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
