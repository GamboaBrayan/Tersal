<?php

namespace App\Http\Controllers;

use App\Models\Tire;
use App\Models\Brand;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;
use Rap2hpoutre\FastExcel\FastExcel;

class DashboardController extends Controller
{
    public function index()
    {
        $totalProducts = Tire::count();
        $outOfStock = Tire::where('stock', 0)->count();

        return Inertia::render('Admin/Dashboard', [
            'totalProducts' => $totalProducts,
            'outOfStock' => $outOfStock,
        ]);
    }

    public function inventory(Request $request)
    {
        $query = Tire::with('brand')->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('model', 'like', "%{$search}%")
                  ->orWhere('width', 'like', "%{$search}%")
                  ->orWhereHas('brand', function($b) use ($search) {
                      $b->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $tires = $query->paginate(15)->withQueryString();

        return Inertia::render('Admin/InventoryList', [
            'tires' => $tires,
            'filters' => $request->only(['search'])
        ]);
    }

    public function create()
    {
        $brands = Brand::orderBy('name')->get();
        return Inertia::render('Admin/ProductForm', [
            'brands' => $brands,
            'tire' => null
        ]);
    }

    public function edit(Tire $tire)
    {
        $brands = Brand::orderBy('name')->get();
        return Inertia::render('Admin/ProductForm', [
            'brands' => $brands,
            'tire' => $tire
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'brand_id' => 'required|exists:brands,id',
            'model' => 'required|string|max:255',
            'year' => 'nullable|string|max:50',
            'version' => 'nullable|string|max:255',
            'width' => 'required|integer',
            'profile' => 'required|integer',
            'rim' => 'required|integer',
            'load_index' => 'required|integer',
            'speed_rating' => 'required|string|max:10',
            'terrain_type' => 'nullable|string|in:H/T,A/T,M/T',
            'is_run_flat' => 'boolean',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'offer_price' => 'nullable|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'status' => 'boolean',
            'images.*' => 'nullable|image|max:2048'
        ]);

        $imagePaths = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $imagePaths[] = $image->store('tires', 'public');
            }
        }
        $validated['images_json'] = $imagePaths;

        Tire::create($validated);

        return redirect()->back()->with('success', 'Neumático creado exitosamente.');
    }

    public function update(Request $request, Tire $tire)
    {
        $validated = $request->validate([
            'brand_id' => 'required|exists:brands,id',
            'model' => 'required|string|max:255',
            'year' => 'nullable|string|max:50',
            'version' => 'nullable|string|max:255',
            'width' => 'required|integer',
            'profile' => 'required|integer',
            'rim' => 'required|integer',
            'load_index' => 'required|integer',
            'speed_rating' => 'required|string|max:10',
            'terrain_type' => 'nullable|string|in:H/T,A/T,M/T',
            'is_run_flat' => 'boolean',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'offer_price' => 'nullable|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'status' => 'boolean',
            'images.*' => 'nullable|image|max:2048',
            'existing_images' => 'nullable|array'
        ]);

        $imagePaths = $request->input('existing_images', []);
        
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $imagePaths[] = $image->store('tires', 'public');
            }
        }
        $validated['images_json'] = $imagePaths;

        $tire->update($validated);

        return redirect()->back()->with('success', 'Neumático actualizado exitosamente.');
    }

    public function destroy(Tire $tire)
    {
        $tire->delete();
        return redirect()->back()->with('success', 'Neumático eliminado exitosamente.');
    }

    public function settings()
    {
        $settings = Setting::all()->pluck('value', 'key');
        
        return Inertia::render('Admin/Settings', [
            'settings' => $settings
        ]);
    }

    public function updateSettings(Request $request)
    {
        $validated = $request->validate([
            'whatsapp_number' => 'nullable|string',
            'faqs' => 'nullable|array'
        ]);

        foreach ($validated as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => is_array($value) ? json_encode($value) : $value]
            );
        }

        return redirect()->back()->with('success', 'Configuración actualizada exitosamente.');
    }

    public function brands(Request $request)
    {
        $query = Brand::withCount('tires')->orderBy('name');
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%");
        }

        $brands = $query->paginate(15)->withQueryString();
        
        return Inertia::render('Admin/Brands', [
            'brands' => $brands,
            'filters' => $request->only(['search'])
        ]);
    }

    public function storeBrand(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:brands,name',
            'logo' => 'nullable|image|max:2048',
            'show_on_home' => 'boolean',
        ]);

        $data = [
            'name' => $validated['name'],
            'show_on_home' => $request->boolean('show_on_home', true),
        ];

        if ($request->hasFile('logo')) {
            $data['logo_url'] = $request->file('logo')->store('brands', 'public');
        }

        Brand::create($data);

        return redirect()->back()->with('success', 'Marca creada exitosamente.');
    }

    public function updateBrand(Request $request, Brand $brand)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:brands,name,' . $brand->id,
            'logo' => 'nullable|image|max:2048',
            'show_on_home' => 'boolean',
        ]);

        $data = [
            'name' => $validated['name'],
            'show_on_home' => $request->boolean('show_on_home', true),
        ];

        if ($request->hasFile('logo')) {
            $data['logo_url'] = $request->file('logo')->store('brands', 'public');
        }

        $brand->update($data);

        return redirect()->back()->with('success', 'Marca actualizada exitosamente.');
    }

    public function destroyBrand(Brand $brand)
    {
        $brand->delete();
        return redirect()->back()->with('success', 'Marca eliminada exitosamente.');
    }

    public function importBrands(Request $request)
    {
        $request->validate(['file' => 'required|file|mimes:xlsx,xls,csv,txt']);
        
        $filePath = $request->file('file')->getRealPath();
        
        $totalRows = 0;
        (new FastExcel)->import($filePath, function ($line) use (&$totalRows) {
            $totalRows++;
        });

        $cacheKey = 'import_progress_' . auth()->id();
        Cache::put($cacheKey, 0);

        $currentRow = 0;
        (new FastExcel)->import($filePath, function ($row) use ($totalRows, &$currentRow, $cacheKey) {
            $brandName = $row['MARCA'] ?? null;
            if ($brandName) {
                Brand::firstOrCreate(
                    ['name' => trim($brandName)],
                    ['show_on_home' => true]
                );
            }
            
            $currentRow++;
            if ($currentRow % 10 === 0 || $currentRow === $totalRows) {
                Cache::put($cacheKey, round(($currentRow / max(1, $totalRows)) * 100));
            }
        });
        
        Cache::forget($cacheKey);
        
        return redirect()->back()->with('success', 'Marcas importadas correctamente.');
    }

    public function importInventory(Request $request)
    {
        $request->validate(['file' => 'required|file|mimes:xlsx,xls,csv,txt']);
        
        $filePath = $request->file('file')->getRealPath();
        
        $totalRows = 0;
        (new FastExcel)->import($filePath, function ($line) use (&$totalRows) {
            $totalRows++;
        });

        $cacheKey = 'import_progress_' . auth()->id();
        Cache::put($cacheKey, 0);

        $currentRow = 0;
        (new FastExcel)->import($filePath, function ($row) use ($totalRows, &$currentRow, $cacheKey) {
            $currentRow++;
            if ($currentRow % 10 === 0 || $currentRow === $totalRows) {
                Cache::put($cacheKey, round(($currentRow / max(1, $totalRows)) * 100));
            }

            $brandName = $row['MARCA'] ?? null;
            if (!$brandName) return;
            
            $brand = Brand::firstOrCreate(
                ['name' => trim($brandName)],
                ['show_on_home' => true]
            );
            
            Tire::create([
                'brand_id' => $brand->id,
                'model' => $row['MODELO'] ?? 'S/M',
                'version' => $row['VERSIÓN'] ?? null,
                'year' => $row['AÑO'] ?? null,
                'width' => intval($row['ANCHO'] ?? 0),
                'profile' => intval($row['ALTO'] ?? 0),
                'rim' => intval($row['RIN'] ?? 0),
                'price' => floatval($row['PRECIO'] ?? 0),
                'stock' => 10,
                'load_index' => 0,
                'speed_rating' => 'N/A',
                'status' => true
            ]);
        });
        
        Cache::forget($cacheKey);
        
        return redirect()->back()->with('success', 'Neumáticos importados correctamente.');
    }

    public function importProgress()
    {
        $progress = Cache::get('import_progress_' . auth()->id(), null);
        return response()->json(['progress' => $progress]);
    }
}
