<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ForumCategories extends Model
{
    use HasFactory;

    public function forums(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Forum::class, 'forum_category_id', 'id');
    }
}
