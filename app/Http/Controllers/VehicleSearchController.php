<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class VehicleSearchController extends Controller
{
    private const BASE_URL = 'https://api.wheel-size.com/v2';

    private static function apiKey(): string
    {
        return (string) config('tersal.wheel_size_api_key', '');
    }

    private static function fetchFromApi(string $endpoint, array $params = []): array
    {
        $key = self::apiKey();
        if ($key === '') {
            return [];
        }

        try {
            $response = Http::connectTimeout(3)
                ->timeout(6)
                ->get(self::BASE_URL . $endpoint, array_merge($params, ['user_key' => $key]));

            if (!$response->successful()) {
                return [];
            }

            $data = $response->json('data') ?? $response->json();

            return is_array($data) ? $data : [];
        } catch (\Throwable $e) {
            return [];
        }
    }

    private static function rememberNonEmpty(string $cacheKey, callable $callback): array
    {
        $cached = Cache::get($cacheKey);
        if (is_array($cached) && !empty($cached)) {
            return $cached;
        }

        $data = $callback();
        if (!empty($data)) {
            Cache::put($cacheKey, $data, now()->addDays(30));
        }

        return $data;
    }

    public function getMakes()
    {
        return self::rememberNonEmpty('ws_makes', function () {
            return self::fetchFromApi('/makes/');
        });
    }

    public function getModels(Request $request)
    {
        $make = $request->make;
        if (!$make) {
            return response()->json([]);
        }

        return self::rememberNonEmpty("ws_models_{$make}", function () use ($make) {
            return self::fetchFromApi('/models/', ['make' => $make]);
        });
    }

    public function getYears(Request $request)
    {
        $make = $request->make;
        $model = $request->model;
        if (!$make || !$model) {
            return response()->json([]);
        }

        return self::rememberNonEmpty("ws_years_{$make}_{$model}", function () use ($make, $model) {
            return self::fetchFromApi('/years/', ['make' => $make, 'model' => $model]);
        });
    }

    public function getTrims(Request $request)
    {
        $make = $request->make;
        $model = $request->model;
        $year = $request->year;
        if (!$make || !$model || !$year) {
            return response()->json([]);
        }

        return self::rememberNonEmpty("ws_trims_{$make}_{$model}_{$year}", function () use ($make, $model, $year) {
            return self::fetchFromApi('/modifications/', [
                'make' => $make,
                'model' => $model,
                'year' => $year,
            ]);
        });
    }

    public static function getTiresForVehicle($make, $model, $year, $trim = null)
    {
        if (self::apiKey() === '') {
            return [];
        }

        $trimKey = $trim ? "_{$trim}" : '';
        $cacheKey = "ws_tires_{$make}_{$model}_{$year}{$trimKey}";

        return self::rememberNonEmpty($cacheKey, function () use ($make, $model, $year, $trim) {
            $params = [
                'make' => $make,
                'model' => $model,
                'year' => $year,
            ];

            if ($trim) {
                $params['modification'] = $trim;
                return self::fetchFromApi('/search/by_model/', $params);
            }

            foreach (['usdm', 'eudm', 'sadm', 'ladm'] as $region) {
                $data = self::fetchFromApi('/search/by_model/', array_merge($params, ['region' => $region]));
                if (!empty($data)) {
                    return $data;
                }
            }

            return [];
        });
    }
}
