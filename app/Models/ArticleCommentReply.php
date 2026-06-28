<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArticleCommentReply extends Model
{
    use HasFactory;
    protected $guarded;
    protected $with = ['votes', 'user'];

    public function votes()
    {
        return $this->hasMany(ArticleCommentReplyVote::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    //create function to check if user already voted on comment
    public function userVoted($user_id)
    {
        return $this->votes()->where('user_id', $user_id)->exists();
    }
}
