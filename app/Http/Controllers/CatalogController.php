<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Tire;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CatalogController extends Controller
{
    public function index(Request $request)
    {
        $query = Tire::with('brand')->where('status', true);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('model', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('brand', function($b) use ($search) {
                      $b->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('width')) {
            $query->where('width', $request->width);
        }
        if ($request->filled('profile')) {
            $query->where('profile', $request->profile);
        }
        if ($request->filled('rim')) {
            $query->where('rim', $request->rim);
        }
        if ($request->filled('brand_id')) {
            $query->where('brand_id', $request->brand_id);
        }
        if ($request->filled('terrain_type')) {
            $query->where('terrain_type', $request->terrain_type);
        }
        if ($request->filled('price_min')) {
            $query->where(function($q) use ($request) {
                $q->where(function($sub) use ($request) {
                    $sub->whereNotNull('offer_price')->where('offer_price', '>=', $request->price_min);
                })->orWhere(function($sub) use ($request) {
                    $sub->whereNull('offer_price')->where('price', '>=', $request->price_min);
                });
            });
        }
        if ($request->filled('price_max')) {
            $query->where(function($q) use ($request) {
                $q->where(function($sub) use ($request) {
                    $sub->whereNotNull('offer_price')->where('offer_price', '<=', $request->price_max);
                })->orWhere(function($sub) use ($request) {
                    $sub->whereNull('offer_price')->where('price', '<=', $request->price_max);
                });
            });
        }

        $tires = $query->latest()->paginate(12)->withQueryString();
        $brands = Brand::orderBy('name')->get();

        return Inertia::render('Catalog/Index', [
            'tires' => $tires,
            'brands' => $brands,
            'filters' => $request->only(['width', 'profile', 'rim', 'brand_id', 'terrain_type', 'price_min', 'price_max', 'search'])
        ]);
    }

    public function show(Tire $tire)
    {
        $tire->load('brand');
        
        $relatedTires = Tire::with('brand')
            ->where('status', true)
            ->where('id', '!=', $tire->id)
            ->where(function($q) use ($tire) {
                $q->where('brand_id', $tire->brand_id)
                  ->orWhere('width', $tire->width);
            })
            ->take(8)
            ->get();
        
        return Inertia::render('ProductDetail/Index', [
            'tire' => $tire,
            'relatedTires' => $relatedTires
        ]);
    }
}
