<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Messages extends Model
{
    use HasFactory;

    protected $guarded;

    public function deletedThatMessage()
    {
        // dd(Auth::user()->id);
        if (Auth()->user()->id == $this->from) {
            return $this->deleted_at_by_user_1 == 1 ? true : false;
        } else {
            return $this->deleted_at_by_user_2 == 1 ? true : false;
        }
    }
    public function deletedByUser()
    {
        if (Auth()->user()->id == $this->from) {
            return $this->block_at_by_user_2 == 1 ? true : false;
        } else {
            return $this->block_at_by_user_1 == 1 ? true : false;
        }
    }
}
