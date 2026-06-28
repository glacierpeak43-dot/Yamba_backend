<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FAQSubcategory extends Model
{
    use HasFactory;

    protected $with =['faq'];

    public function faq(){
        return $this->hasMany(FrequentlyAskedQuestions::class ,'f_a_q_subcategories');
    }

    public function category(){
        return $this->belongsTo(FAQCategory::class ,'f_a_q_subcategories' );
    }
}
