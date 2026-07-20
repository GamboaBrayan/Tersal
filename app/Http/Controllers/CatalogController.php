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

        $isVehicleSearch = $request->filled('vehicle_make') && $request->filled('vehicle_model') && $request->filled('vehicle_year');
        
        $recommendedSizes = [];
        $alternativeSizes = [];
        $recommendedTires = [];
        $alternativeTires = [];

        if ($isVehicleSearch) {
            $apiData = \App\Http\Controllers\VehicleSearchController::getTiresForVehicle(
                $request->vehicle_make,
                $request->vehicle_model,
                $request->vehicle_year,
                $request->vehicle_trim // Can be null
            );
            
            if (is_array($apiData)) {
                foreach ($apiData as $config) {
                    if (isset($config['wheels'])) {
                        foreach ($config['wheels'] as $wheel) {
                            $isStock = isset($wheel['is_stock']) ? $wheel['is_stock'] : true;
                            
                            $axes = ['front', 'rear'];
                            
                            // Check if front and rear tires are exactly the same
                            $sameTires = false;
                            if (isset($wheel['front']['tire']) && isset($wheel['rear']['tire'])) {
                                if ($wheel['front']['tire'] === $wheel['rear']['tire']) {
                                    $sameTires = true;
                                }
                            }
                            
                            foreach ($axes as $axis) {
                                if (isset($wheel[$axis]['tire'])) {
                                    $sizeStr = $wheel[$axis]['tire'];
                                    // Match e.g. "185/65R15" or "185/65ZR15"
                                    preg_match('/^(\d+)\/(\d+)[A-Z]+(\d+)/i', $sizeStr, $matches);
                                    if (count($matches) >= 4) {
                                        $sizeObj = [
                                            'width' => $matches[1],
                                            'profile' => $matches[2],
                                            'rim' => $matches[3],
                                            'axis' => $sameTires ? 'both' : $axis
                                        ];
                                        
                                        if ($isStock) {
                                            $recommendedSizes[] = $sizeObj;
                                        } else {
                                            $alternativeSizes[] = $sizeObj;
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
            
            // Remove duplicates
            $recommendedSizes = array_unique($recommendedSizes, SORT_REGULAR);
            $alternativeSizes = array_unique($alternativeSizes, SORT_REGULAR);
            
            // Fetch tires for recommended
            if (count($recommendedSizes) > 0) {
                $recQuery = Tire::with('brand')->where('status', true);
                $recQuery->where(function($q) use ($recommendedSizes) {
                    foreach ($recommendedSizes as $size) {
                        $q->orWhere(function($sub) use ($size) {
                            $sub->where('width', $size['width'])
                                ->where('profile', $size['profile'])
                                ->where('rim', $size['rim']);
                        });
                    }
                });
                $recommendedTires = $recQuery->get();
                $recommendedTires->map(function ($tire) use ($recommendedSizes) {
                    foreach ($recommendedSizes as $size) {
                        if ($tire->width == $size['width'] && $tire->profile == $size['profile'] && $tire->rim == $size['rim']) {
                            $tire->setAttribute('axis', $size['axis']);
                        }
                    }
                    return $tire;
                });
            }
            
            // Fetch tires for alternatives
            if (count($alternativeSizes) > 0) {
                $altQuery = Tire::with('brand')->where('status', true);
                $altQuery->where(function($q) use ($alternativeSizes) {
                    foreach ($alternativeSizes as $size) {
                        $q->orWhere(function($sub) use ($size) {
                            $sub->where('width', $size['width'])
                                ->where('profile', $size['profile'])
                                ->where('rim', $size['rim']);
                        });
                    }
                });
                $alternativeTires = $altQuery->get();
                $alternativeTires->map(function ($tire) use ($alternativeSizes) {
                    foreach ($alternativeSizes as $size) {
                        if ($tire->width == $size['width'] && $tire->profile == $size['profile'] && $tire->rim == $size['rim']) {
                            $tire->setAttribute('axis', $size['axis']);
                        }
                    }
                    return $tire;
                });
            }
            
            $tires = null; // No normal pagination for vehicle search
        } else {
            $tires = $query->latest()->paginate(12)->withQueryString();
        }
        $brands = Brand::orderBy('name')->get();
        
        $widths = Tire::where('status', true)->whereNotNull('width')->distinct()->orderBy('width')->pluck('width');
        $profiles = Tire::where('status', true)->whereNotNull('profile')->distinct()->orderBy('profile')->pluck('profile');
        $rims = Tire::where('status', true)->whereNotNull('rim')->distinct()->orderBy('rim')->pluck('rim');

        return Inertia::render('Catalog/Index', [
            'tires' => $tires,
            'isVehicleSearch' => $isVehicleSearch,
            'recommendedTires' => $recommendedTires,
            'alternativeTires' => $alternativeTires,
            'recommendedSizes' => array_values($recommendedSizes),
            'alternativeSizes' => array_values($alternativeSizes),
            'brands' => $brands,
            'widths' => $widths,
            'profiles' => $profiles,
            'rims' => $rims,
            'filters' => $request->only(['width', 'profile', 'rim', 'brand_id', 'terrain_type', 'price_min', 'price_max', 'search', 'vehicle_make', 'vehicle_model', 'vehicle_year', 'vehicle_trim'])
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
