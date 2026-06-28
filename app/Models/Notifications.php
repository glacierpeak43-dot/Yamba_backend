<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notifications extends Model
{
    protected $guarded;
    use HasFactory;

    public function friendRequest()
    {
        return $this->belongsTo(FriendRequests::class);
    }

    public function location()
    {
        return $this->belongsTo(Location::class, 'locations');
    }
}
