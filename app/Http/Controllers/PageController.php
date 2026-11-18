<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;

class PageController extends Controller
{
    public function guide()
    {
        $faqs = Cache::rememberForever('settings.faqs', function () {
            $faqsSetting = Setting::where('key', 'faqs')->first();
            return $faqsSetting ? (json_decode($faqsSetting->value, true) ?? []) : [];
        });

        return Inertia::render('Guide/Index', [
            'faqs' => $faqs,
        ]);
    }

    public function contact()
    {
        return Inertia::render('Contact/Index');
    }

    public function about()
    {
        return Inertia::render('About/Index');
    }

    public function terms()
    {
        return Inertia::render('Legal/Terms');
    }

    public function privacy()
    {
        return Inertia::render('Legal/Privacy');
    }

    public function complaintsBook()
    {
        return Inertia::render('Legal/LibroReclamaciones');
    }

    public function fallback()
    {
        return Inertia::render('Error/404');
    }
}

