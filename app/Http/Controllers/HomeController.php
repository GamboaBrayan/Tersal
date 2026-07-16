<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Tire;
use Illuminate\Http\Request;
use Inertia\Inertia;

class HomeController extends Controller
{
    public function index()
    {
        $brands = Brand::where('show_on_home', true)->orderBy('name')->get();
        
        $promotions = Tire::with('brand')
            ->whereNotNull('offer_price')
            ->where('status', true)
            ->latest()
            ->take(6)
            ->get();

        $widths = Tire::select('width')->distinct()->orderBy('width')->pluck('width');
        $profiles = Tire::select('profile')->distinct()->orderBy('profile')->pluck('profile');
        $rims = Tire::select('rim')->distinct()->orderBy('rim')->pluck('rim');

        return Inertia::render('Home/Index', [
            'brands' => $brands,
            'promotions' => $promotions,
            'widths' => $widths,
            'profiles' => $profiles,
            'rims' => $rims
        ]);
    }
}
