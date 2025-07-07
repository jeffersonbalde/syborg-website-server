<?php

namespace App\Http\Controllers\client;

use App\Http\Controllers\Controller;
use App\Models\HeroSlider;
use Illuminate\Http\Request;

class ClientHeroSliderController extends Controller
{
    public function index() {   
        $heroSlider = HeroSlider::orderBy("created_at", "DESC")->get();
        return $heroSlider;
    }
}
