<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class VehicleSearchController extends Controller
{
    private $baseUrl = 'https://api.wheel-size.com/v2';
    
    private function getKey()
    {
        return env('WHEEL_SIZE_API_KEY', '');
    }

    public function getMakes()
    {
        return Cache::remember('ws_makes', now()->addDays(30), function () {
            $response = Http::get("{$this->baseUrl}/makes/", [
                'user_key' => $this->getKey()
            ]);
            if (!$response->successful()) return [];
            return $response->json('data') ?? $response->json();
        });
    }

    public function getModels(Request $request)
    {
        $make = $request->make;
        if (!$make) return response()->json([]);

        return Cache::remember("ws_models_{$make}", now()->addDays(30), function () use ($make) {
            $response = Http::get("{$this->baseUrl}/models/", [
                'make' => $make,
                'user_key' => $this->getKey()
            ]);
            if (!$response->successful()) return [];
            return $response->json('data') ?? $response->json();
        });
    }

    public function getYears(Request $request)
    {
        $make = $request->make;
        $model = $request->model;
        if (!$make || !$model) return response()->json([]);

        return Cache::remember("ws_years_{$make}_{$model}", now()->addDays(30), function () use ($make, $model) {
            $response = Http::get("{$this->baseUrl}/years/", [
                'make' => $make,
                'model' => $model,
                'user_key' => $this->getKey()
            ]);
            if (!$response->successful()) return [];
            return $response->json('data') ?? $response->json();
        });
    }

    public function getTrims(Request $request)
    {
        $make = $request->make;
        $model = $request->model;
        $year = $request->year;
        if (!$make || !$model || !$year) return response()->json([]);

        return Cache::remember("ws_trims_{$make}_{$model}_{$year}", now()->addDays(30), function () use ($make, $model, $year) {
            $response = Http::get("{$this->baseUrl}/trims/", [
                'make' => $make,
                'model' => $model,
                'year' => $year,
                'user_key' => $this->getKey()
            ]);
            if (!$response->successful()) return [];
            return $response->json('data') ?? $response->json();
        });
    }

    public static function getTiresForVehicle($make, $model, $year, $trim = null)
    {
        $key = env('WHEEL_SIZE_API_KEY', '');
        if (!$key) return [];
        
        $trimKey = $trim ? "_{$trim}" : "";
        $cacheKey = "ws_tires_{$make}_{$model}_{$year}{$trimKey}";

        $data = Cache::remember($cacheKey, now()->addDays(30), function () use ($make, $model, $year, $trim, $key) {
            $params = [
                'make' => $make,
                'model' => $model,
                'year' => $year,
                'user_key' => $key
            ];
            
            if ($trim) {
                $params['modification'] = $trim;
                $response = Http::get("https://api.wheel-size.com/v2/search/by_model/", $params);
                if ($response->successful()) {
                    return $response->json('data') ?? [];
                }
            } else {
                // Optimize: query by default usdm region, fallback to eudm
                $params['region'] = 'usdm';
                $response = Http::get("https://api.wheel-size.com/v2/search/by_model/", $params);
                if ($response->successful() && !empty($response->json('data'))) {
                    return $response->json('data');
                }
                
                // Fallback
                $params['region'] = 'eudm';
                $res2 = Http::get("https://api.wheel-size.com/v2/search/by_model/", $params);
                if ($res2->successful()) {
                    return $res2->json('data') ?? [];
                }
            }
            
            return [];
        });

        // Si falló la API y trajo vacío, lo borramos de caché para que reintente luego
        if (empty($data)) {
            Cache::forget($cacheKey);
        }

        return $data;
    }
}
