<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserProfile extends Model
{
    use HasFactory;

    protected $guarded;

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function articleViews()
    {
        return $this->hasMany(ProfileArticleView::class);
    }

    //create function to check if article aready viewed by user
    public function isArticleViewed($articleId)
    {
        return $this->articleViews()->where('article_id', $articleId)->exists();
    }

    //create function to get profile level
    public function getProfileLevel()
    {
        $articleViews = $this->articleViews()->count();
        $levels = Level::all();

        foreach ($levels as $level) {
            if ($this->isInRange($articleViews, $level->min, $level->max)) {
                return $level;
            }
        }
    }

    //create function to check in number is in range
    public function isInRange($number, $min, $max)
    {
        if ($number >= $min && $number <= $max) {
            return true;
        }
        return false;
    }

    //create function to determine if user is level up
    public function isLevelUp()
    {
        $level = $this->getProfileLevel();
        if ($level->id > $this->level_id) {
            return true;
        }
        return false;
    }

    //update level
    public function updateLevel()
    {
        $level = $this->getProfileLevel();

        // dd($level->level_number . ' ' . $this->level_number);
        if ($level->level_number > $this->level_number) {
            $this->update([
                'level_number' => $level->level_number,
            ]);
            return true;
        }
        return false;
    }
}
