<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FAQCategory extends Model
{
    use HasFactory;


    protected $with =['faqSubcategories'];

    public function faqSubcategories(){
        return $this->hasMany(FAQSubcategory::class ,'f_a_q_subcategories' );
    }
}
