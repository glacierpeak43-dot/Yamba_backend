<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    use HasFactory;

    protected $guarded;

    public function messages()
    {
        return $this->hasMany(Messages::class);
    }

    //create function to check if chat aready exists
    public function chatExists($user_id, $friend_id)
    {
        return $this->where('user_id', $user_id)->where('friend_id', $friend_id)->exists();
    }

    //create function to check if user has blocked you
    public function blockedThatChat($user_id)
    {
        if ($this->user_1 == $user_id) {
            return $this->block_at_by_user_1 == 1 ? true : false;
        }
        return $this->block_at_by_user_2 == 1 ? true : false;
    }
    public function blockedByUser($friend)
    {
        if ($this->user_2 == $friend) {
            return $this->block_at_by_user_2 == 1 ? true : false;
        }
        return $this->block_at_by_user_1 == 1 ? true : false;
    }
}
