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
        $brands = \Illuminate\Support\Facades\Cache::remember('brands.home', 86400, function() {
            return Brand::where('show_on_home', true)->orderBy('name')->get();
        });
        
        $promotions = \Illuminate\Support\Facades\Cache::remember('promotions.home', 86400, function() {
            return Tire::with(['brand', 'category'])
                ->where('is_promoted', true)
                ->where('status', true)
                ->latest()
                ->take(6)
                ->get();
        });

        $widths = \Illuminate\Support\Facades\Cache::remember('tires.widths', 86400, function() {
            return Tire::select('width')->distinct()->orderBy('width')->pluck('width');
        });
        
        $profiles = \Illuminate\Support\Facades\Cache::remember('tires.profiles', 86400, function() {
            return Tire::select('profile')->distinct()->orderBy('profile')->pluck('profile');
        });
        
        $rims = \Illuminate\Support\Facades\Cache::remember('tires.rims', 86400, function() {
            return Tire::select('rim')->distinct()->orderBy('rim')->pluck('rim');
        });

        $heroImagesSetting = \App\Models\Setting::where('key', 'hero_images')->first();
        $heroImages = [];
        if ($heroImagesSetting) {
            $paths = json_decode($heroImagesSetting->value, true);
            if (is_array($paths)) {
                $heroImages = array_map(function($path) {
                    if (str_starts_with($path, 'http')) return $path;
                    return \Illuminate\Support\Facades\Storage::disk('r2')->url($path);
                }, $paths);
            }
        }

        return Inertia::render('Home/Index', [
            'brands' => $brands,
            'promotions' => $promotions,
            'widths' => $widths,
            'profiles' => $profiles,
            'rims' => $rims,
            'heroImages' => $heroImages
        ]);
    }
}
