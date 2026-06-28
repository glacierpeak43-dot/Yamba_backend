<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArticleCommentReport extends Model
{
    protected $guarded;
    use HasFactory;

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function reportedBy()
    {
        return $this->belongsTo(User::class, 'reported_by', 'id');
    }
    public function comment()
    {
        return $this->belongsTo(ArticleComment::class);
    }
}
