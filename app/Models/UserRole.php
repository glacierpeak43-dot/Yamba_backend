<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserRole extends Model
{
    use HasFactory;

    protected $guarded;

    protected $with =['role'];

    //create fuction for role relationship
    public function role()
    {
        return $this->belongsTo(Role::class);
    }
}
