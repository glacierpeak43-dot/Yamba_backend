<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    use HasFactory;
    protected $guarded;

    public function user()
    {
        return $this->belongsTo(User::class, "user_id", "id");
    }
    public function counsellor()
    {
        return $this->belongsTo(User::class, "consellor_id", "id");
    }
}
