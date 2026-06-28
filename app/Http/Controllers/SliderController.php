<?php

namespace App\Http\Controllers;

use App\Models\AppCarouselPictures;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SliderController extends Controller
{
    public function getSliders(){
        $images = AppCarouselPictures::where('university_id', Auth::user()->university_id)->get();
        return response(['success' => true, 'images' => $images,]);

    }
}
