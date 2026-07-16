<?php

namespace App\Http\Controllers;

use App\Models\Tire;
use App\Models\Brand;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

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

    public function inventory()
    {
        $tires = Tire::with('brand')->latest()->get();

        return Inertia::render('Admin/InventoryList', [
            'tires' => $tires
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

    public function brands()
    {
        $brands = Brand::withCount('tires')->orderBy('name')->get();
        return Inertia::render('Admin/Brands', [
            'brands' => $brands
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
}
