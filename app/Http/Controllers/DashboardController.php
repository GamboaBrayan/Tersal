<?php

namespace App\Http\Controllers;

use App\Models\Tire;
use App\Models\Brand;
use App\Models\Setting;
use App\Models\Category;
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
        $categories = Category::orderBy('order')->orderBy('name')->get();
        return Inertia::render('Admin/ProductForm', [
            'brands' => $brands,
            'categories' => $categories,
            'tire' => null
        ]);
    }

    public function edit(Tire $tire)
    {
        $brands = Brand::orderBy('name')->get();
        $categories = Category::orderBy('order')->orderBy('name')->get();
        return Inertia::render('Admin/ProductForm', [
            'brands' => $brands,
            'categories' => $categories,
            'tire' => $tire
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'brand_id' => 'required|exists:brands,id',
            'category_id' => 'nullable|exists:categories,id',
            'model' => 'required|string|max:255',
            'year' => 'nullable|string|max:50',
            'version' => 'nullable|string|max:255',
            'width' => 'required|integer',
            'profile' => 'nullable|integer',
            'rim' => 'required|integer',
            'load_index' => 'required|string|max:50',
            'speed_rating' => 'required|string|max:10',
            'terrain_type' => 'nullable|string|in:H/T,A/T,M/T',
            'is_run_flat' => 'boolean',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'offer_price' => 'nullable|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'status' => 'boolean',
            'images.*' => 'nullable|image|max:2048',
            'image_urls' => 'nullable|array',
            'image_urls.*' => 'nullable|url'
        ]);

        $imagePaths = [];
        
        // Add URLs
        if ($request->has('image_urls') && is_array($request->image_urls)) {
            foreach ($request->image_urls as $url) {
                if (!empty($url)) {
                    $imagePaths[] = $url;
                }
            }
        }

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $imagePaths[] = $image->store('tires', 'r2');
            }
        }
        $validated['images_json'] = $imagePaths;
        $validated['profile'] = $validated['profile'] ?? 0;

        Tire::create($validated);

        return redirect('/admin/inventory')->with('success', 'Neumático creado exitosamente.');
    }

    public function update(Request $request, Tire $tire)
    {
        $validated = $request->validate([
            'brand_id' => 'required|exists:brands,id',
            'category_id' => 'nullable|exists:categories,id',
            'model' => 'required|string|max:255',
            'year' => 'nullable|string|max:50',
            'version' => 'nullable|string|max:255',
            'width' => 'required|integer',
            'profile' => 'nullable|integer',
            'rim' => 'required|integer',
            'load_index' => 'required|string|max:50',
            'speed_rating' => 'required|string|max:10',
            'terrain_type' => 'nullable|string|in:H/T,A/T,M/T',
            'is_run_flat' => 'boolean',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'offer_price' => 'nullable|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'status' => 'boolean',
            'images.*' => 'nullable|image|max:2048',
            'existing_images' => 'nullable|array',
            'image_urls' => 'nullable|array',
            'image_urls.*' => 'nullable|url'
        ]);

        $existingImagesInput = $request->input('existing_images', []);
        
        $originalImages = is_array($tire->images_json) ? $tire->images_json : [];
        $deletedImages = array_diff($originalImages, $existingImagesInput);
        
        foreach ($deletedImages as $delImage) {
            if (!empty($delImage) && !str_starts_with($delImage, 'http')) {
                try {
                    \Illuminate\Support\Facades\Storage::disk('r2')->delete($delImage);
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error("Failed to delete image from R2: {$delImage}", ['error' => $e->getMessage()]);
                }
            }
        }

        $imagePaths = $existingImagesInput;
        // Add new URLs
        if ($request->has('image_urls') && is_array($request->image_urls)) {
            foreach ($request->image_urls as $url) {
                if (!empty($url)) {
                    $imagePaths[] = $url;
                }
            }
        }
        
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $imagePaths[] = $image->store('tires', 'r2');
            }
        }
        $validated['images_json'] = $imagePaths;
        $validated['profile'] = $validated['profile'] ?? 0;

        $tire->update($validated);

        return redirect('/admin/inventory')->with('success', 'Neumático actualizado exitosamente.');
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
            'faqs' => 'nullable|array',
            'hero_images' => 'nullable|array'
        ]);

        if (isset($validated['hero_images'])) {
            $processedImages = [];
            foreach ($validated['hero_images'] as $image) {
                if (is_string($image)) {
                    $processedImages[] = $image;
                } elseif ($image instanceof \Illuminate\Http\UploadedFile) {
                    $processedImages[] = $image->store('hero', 'r2');
                }
            }
            
            $oldSetting = Setting::where('key', 'hero_images')->first();
            if ($oldSetting) {
                $oldImages = json_decode($oldSetting->value, true);
                if (is_array($oldImages)) {
                    $deletedHeroImages = array_diff($oldImages, array_filter($processedImages, 'is_string'));
                    foreach ($deletedHeroImages as $delImage) {
                        if (!empty($delImage) && !str_starts_with($delImage, 'http')) {
                            try {
                                \Illuminate\Support\Facades\Storage::disk('r2')->delete($delImage);
                            } catch (\Exception $e) {
                                \Illuminate\Support\Facades\Log::error("Failed to delete hero image from R2: {$delImage}", ['error' => $e->getMessage()]);
                            }
                        }
                    }
                }
            }

            $validated['hero_images'] = $processedImages;
        }

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
            'logo_url_input' => 'nullable|url',
            'show_on_home' => 'boolean',
        ]);

        $data = [
            'name' => $validated['name'],
            'show_on_home' => $request->boolean('show_on_home', true),
        ];

        if ($request->hasFile('logo')) {
            $data['logo_url'] = $request->file('logo')->store('brands', 'r2');
        } elseif (!empty($validated['logo_url_input'])) {
            $data['logo_url'] = $validated['logo_url_input'];
        }

        Brand::create($data);

        return redirect()->back()->with('success', 'Marca creada exitosamente.');
    }

    public function updateBrand(Request $request, Brand $brand)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:brands,name,' . $brand->id,
            'logo' => 'nullable|image|max:2048',
            'logo_url_input' => 'nullable|url',
            'show_on_home' => 'boolean',
        ]);

        $data = [
            'name' => $validated['name'],
            'show_on_home' => $request->boolean('show_on_home', true),
        ];

        if ($request->hasFile('logo')) {
            if (!empty($brand->logo_url) && !str_starts_with($brand->logo_url, 'http')) {
                try {
                    \Illuminate\Support\Facades\Storage::disk('r2')->delete($brand->logo_url);
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error("Failed to delete brand logo from R2: {$brand->logo_url}", ['error' => $e->getMessage()]);
                }
            }
            $data['logo_url'] = $request->file('logo')->store('brands', 'r2');
        } elseif (!empty($validated['logo_url_input'])) {
            if ($validated['logo_url_input'] !== $brand->logo_url && !empty($brand->logo_url) && !str_starts_with($brand->logo_url, 'http')) {
                try {
                    \Illuminate\Support\Facades\Storage::disk('r2')->delete($brand->logo_url);
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error("Failed to delete brand logo from R2: {$brand->logo_url}", ['error' => $e->getMessage()]);
                }
            }
            $data['logo_url'] = $validated['logo_url_input'];
        }

        $brand->update($data);

        return redirect()->back()->with('success', 'Marca actualizada exitosamente.');
    }

    public function destroyBrand(Brand $brand)
    {
        $brand->delete(); // This deletes associated tires due to onDelete('cascade') in migration if configured, else need to manually delete tires.
        return redirect()->back()->with('success', 'Marca eliminada exitosamente.');
    }

    public function categories(Request $request)
    {
        $query = Category::withCount('tires');

        if ($request->has('search') && $request->search != '') {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $categories = $query->paginate(15)->withQueryString();

        return Inertia::render('Admin/Categories', [
            'categories' => $categories,
            'filters' => $request->only('search'),
        ]);
    }

    public function storeCategory(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name'
        ]);

        $maxOrder = Category::max('order') ?? 0;

        Category::create([
            'name' => $request->name,
            'order' => $maxOrder + 1
        ]);

        return redirect()->back()->with('success', 'Categoría creada exitosamente.');
    }

    public function moveCategory(Category $category, Request $request)
    {
        $direction = $request->input('direction');

        if ($direction === 'up') {
            $swap = Category::where('order', '<', $category->order)->orderBy('order', 'desc')->first();
        } else {
            $swap = Category::where('order', '>', $category->order)->orderBy('order', 'asc')->first();
        }

        if ($swap) {
            $tempOrder = $category->order;
            // Disable global scope internally for update if needed, but update() works fine.
            $category->update(['order' => $swap->order]);
            $swap->update(['order' => $tempOrder]);
        }

        return redirect()->back();
    }

    public function updateCategory(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id
        ]);

        $category->update([
            'name' => $request->name
        ]);

        return redirect()->back()->with('success', 'Categoría actualizada exitosamente.');
    }

    public function destroyCategory(Category $category)
    {
        $category->tires()->delete();
        $category->delete();
        
        return redirect()->back()->with('success', 'Categoría eliminada exitosamente.');
    }

    public function downloadTemplateBrands()
    {
        $brands = Brand::all();
        $data = [];
        
        foreach ($brands as $brand) {
            $data[] = [
                'ID' => $brand->id,
                'MARCA *' => $brand->name,
                'LOGO_URL' => $brand->logo_url
            ];
        }

        if (empty($data)) {
            $data[] = [
                'ID' => '',
                'MARCA *' => '',
                'LOGO_URL' => ''
            ];
        }

        return (new \Rap2hpoutre\FastExcel\FastExcel(collect($data)))->download('Plantilla_Marcas.xlsx');
    }

    public function importBrands(Request $request)
    {
        $request->validate(['file' => 'required|file|mimes:xlsx,xls,csv,txt']);
        
        $filePath = $request->file('file')->getRealPath();
        
        $totalRows = 0;
        (new \Rap2hpoutre\FastExcel\FastExcel)->import($filePath, function ($line) use (&$totalRows) {
            $totalRows++;
        });

        $cacheKey = 'import_progress_' . auth()->id();
        Cache::put($cacheKey, 0);

        $currentRow = 0;
        $errors = [];
        (new \Rap2hpoutre\FastExcel\FastExcel)->import($filePath, function ($row) use ($totalRows, &$currentRow, $cacheKey, &$errors) {
            $currentRow++;
            if ($currentRow % 10 === 0 || $currentRow === $totalRows) {
                Cache::put($cacheKey, round(($currentRow / max(1, $totalRows)) * 100));
            }

            $id = $row['ID'] ?? null;
            $brandName = $row['MARCA *'] ?? $row['MARCA (Obligatorio)'] ?? $row['MARCA'] ?? null;
            $logoUrl = $row['LOGO_URL'] ?? $row['LOGO_URL (Opcional)'] ?? null;
            
            if (!empty($id)) {
                $brand = Brand::find($id);
                if (!$brand) {
                    $errors[] = "Fila $currentRow: La marca con ID $id no existe.";
                    return;
                }
                if ($brandName) $brand->name = trim($brandName);
                if (isset($row['LOGO_URL']) || isset($row['LOGO_URL (Opcional)'])) {
                    $brand->logo_url = trim($logoUrl);
                }
                $brand->save();
            } else if ($brandName) {
                $brand = Brand::firstOrCreate(
                    ['name' => trim($brandName)],
                    ['show_on_home' => true]
                );
                
                if (!empty($logoUrl)) {
                    $brand->update(['logo_url' => trim($logoUrl)]);
                }
            }
        });
        
        Cache::forget($cacheKey);
        
        if (count($errors) > 0) {
            return redirect()->back()->with('error', 'Se importaron algunos registros, pero hubo errores: ' . implode(' | ', array_slice($errors, 0, 5)));
        }

        return redirect()->back()->with('success', 'Marcas importadas correctamente.');
    }

    public function downloadTemplateInventory()
    {
        $tires = Tire::with(['brand', 'category'])->where('is_promo_only', false)->get();
        $data = [];

        foreach ($tires as $tire) {
            $images = is_array($tire->images_json) ? $tire->images_json : [];
            $data[] = [
                'ID' => $tire->id,
                'MARCA *' => $tire->brand ? $tire->brand->name : '',
                'CATEGORIA *' => $tire->category ? $tire->category->name : '',
                'MODELO *' => $tire->model,
                'VERSIÓN' => $tire->version,
                'AÑO' => $tire->year,
                'ANCHO *' => $tire->width,
                'ALTO *' => $tire->profile,
                'RIN *' => $tire->rim,
                'PRECIO *' => $tire->price,
                'PRECIO_OFERTA' => $tire->offer_price,
                'STOCK *' => $tire->stock,
                'INDICE_CARGA *' => $tire->load_index,
                'INDICE_VELOCIDAD *' => $tire->speed_rating,
                'TIPO_TERRENO' => $tire->terrain_type,
                'RUN_FLAT' => $tire->is_run_flat ? 'SI' : 'NO',
                'DESCRIPCION' => $tire->description,
                'IMAGEN_1' => $images[0] ?? '',
                'IMAGEN_2' => $images[1] ?? '',
                'IMAGEN_3' => $images[2] ?? ''
            ];
        }

        if (empty($data)) {
            $data[] = [
                'ID' => '',
                'MARCA *' => '',
                'CATEGORIA *' => '',
                'MODELO *' => '',
                'VERSIÓN' => '',
                'AÑO' => '',
                'ANCHO *' => '',
                'ALTO *' => '',
                'RIN *' => '',
                'PRECIO *' => '',
                'PRECIO_OFERTA' => '',
                'STOCK *' => '',
                'INDICE_CARGA *' => '',
                'INDICE_VELOCIDAD *' => '',
                'TIPO_TERRENO' => '',
                'RUN_FLAT' => '',
                'DESCRIPCION' => '',
                'IMAGEN_1' => '',
                'IMAGEN_2' => '',
                'IMAGEN_3' => ''
            ];
        }

        $categoriasValidas = Category::pluck('name')->implode(', ');
        $instructions = [
            ['CAMPO' => 'ID', 'EXPLICACIÓN' => 'IMPORTANTE: No modificar si se está editando un registro existente. Dejar vacío para crear uno nuevo.'],
            ['CAMPO' => 'CATEGORIA *', 'EXPLICACIÓN' => "Obligatorio. Debe ser idéntica a una categoría de tu sistema (ej: $categoriasValidas)."],
            ['CAMPO' => 'TIPO_TERRENO', 'EXPLICACIÓN' => 'Opcional. Colocar: H/T (Carretera), A/T (Todo Terreno) o M/T (Lodo/Barro).'],
            ['CAMPO' => 'RUN_FLAT', 'EXPLICACIÓN' => 'Opcional. Colocar "SI" si tiene tecnología Run Flat, de lo contrario dejar vacío.'],
            ['CAMPO' => 'INDICE_CARGA *', 'EXPLICACIÓN' => 'Obligatorio. Número de 2-3 dígitos (Ej: 91, 102).'],
            ['CAMPO' => 'INDICE_VELOCIDAD *', 'EXPLICACIÓN' => 'Obligatorio. Letra (Ej: H, V, W, Y).'],
            ['CAMPO' => 'IMAGEN_1, IMAGEN_2, IMAGEN_3', 'EXPLICACIÓN' => 'Opcional. Pegar el link (http://...) de la foto. Si no tienes, déjalo vacío.'],
            ['CAMPO' => '*', 'EXPLICACIÓN' => 'Los campos con (*) son obligatorios, si faltan, esa llanta no se importará.']
        ];

        $sheets = new \Rap2hpoutre\FastExcel\SheetCollection([
            'Instrucciones' => collect($instructions),
            'Plantilla Datos' => collect($data)
        ]);

        return (new \Rap2hpoutre\FastExcel\FastExcel($sheets))->download('Plantilla_Neumaticos.xlsx');
    }

    public function importInventory(Request $request)
    {
        $request->validate(['file' => 'required|file|mimes:xlsx,xls,csv,txt']);
        
        $filePath = $request->file('file')->getRealPath();
        
        // Detectar si la primera hoja es de Instrucciones
        $sheetIndex = 1;
        try {
            (new FastExcel)->import($filePath, function ($row) use (&$sheetIndex) {
                if (isset($row['CAMPO']) || isset($row['EXPLICACIÓN'])) {
                    $sheetIndex = 2;
                }
                throw new \Exception("StopLoop"); // Cortar iteración luego de la primera fila
            });
        } catch (\Exception $e) {
            // Ignorar excepción de corte
        }

        $totalRows = 0;
        (new FastExcel)->sheet($sheetIndex)->import($filePath, function ($line) use (&$totalRows) {
            $totalRows++;
        });

        $cacheKey = 'import_progress_' . auth()->id();
        Cache::put($cacheKey, 0);

        $categoriesMap = Category::pluck('id', 'name')->mapWithKeys(function ($id, $name) {
            return [strtoupper(trim($name)) => $id];
        })->toArray();

        $currentRow = 0;
        $errors = [];
        (new FastExcel)->sheet($sheetIndex)->import($filePath, function ($row) use ($totalRows, &$currentRow, $cacheKey, $categoriesMap, &$errors) {
            $currentRow++;
            if ($currentRow % 10 === 0 || $currentRow === $totalRows) {
                Cache::put($cacheKey, round(($currentRow / max(1, $totalRows)) * 100));
            }

            if ($currentRow === 1) {
                \Log::info("Row 1 keys: ", array_keys((array)$row));
            }

            $brandName = $row['MARCA *'] ?? $row['MARCA (Obligatorio)'] ?? $row['MARCA'] ?? null;
            if (empty($brandName)) {
                $errors[] = "Fila $currentRow: La columna de Marca está vacía o el archivo tiene un formato inválido.";
                return;
            }

            $categoryName = $row['CATEGORIA *'] ?? $row['CATEGORIA (Obligatorio - Autos, Camionetas, Camiones, OTR)'] ?? $row['CATEGORIA (Opcional - Autos, Camionetas, Camiones, OTR)'] ?? $row['CATEGORIA'] ?? null;
            $categoryId = null;
            if (empty($categoryName)) {
                $errors[] = "Fila $currentRow: La categoría es obligatoria.";
                return;
            } else {
                $cleanCat = strtoupper(trim($categoryName));
                if (!isset($categoriesMap[$cleanCat])) {
                    $errors[] = "Fila $currentRow: Categoría '{$categoryName}' no existe. Solo se permite: " . implode(', ', array_keys($categoriesMap));
                    return;
                }
                $categoryId = $categoriesMap[$cleanCat];
            }
            
            $brand = Brand::firstOrCreate(
                ['name' => trim($brandName)],
                ['show_on_home' => true]
            );
            
            $model = $row['MODELO *'] ?? $row['MODELO (Obligatorio)'] ?? $row['MODELO'] ?? 'S/M';
            $version = $row['VERSIÓN'] ?? $row['VERSIÓN (Opcional)'] ?? null;
            $year = $row['AÑO'] ?? $row['AÑO (Opcional)'] ?? null;
            $width = intval($row['ANCHO *'] ?? $row['ANCHO (Obligatorio)'] ?? $row['ANCHO'] ?? 0);
            $profile = intval($row['ALTO *'] ?? $row['ALTO (Obligatorio)'] ?? $row['ALTO'] ?? 0);
            $rim = intval($row['RIN *'] ?? $row['RIN (Obligatorio)'] ?? $row['RIN'] ?? 0);
            $price = floatval($row['PRECIO *'] ?? $row['PRECIO (Obligatorio)'] ?? $row['PRECIO'] ?? 0);
            $offerPrice = !empty($row['PRECIO_OFERTA']) ? floatval($row['PRECIO_OFERTA']) : (!empty($row['PRECIO_OFERTA (Opcional)']) ? floatval($row['PRECIO_OFERTA (Opcional)']) : null);
            $stock = intval($row['STOCK *'] ?? $row['STOCK (Obligatorio)'] ?? $row['STOCK'] ?? 10);
            $loadIndex = intval($row['INDICE_CARGA *'] ?? $row['INDICE_CARGA (Obligatorio)'] ?? $row['INDICE_CARGA'] ?? 0);
            $speedRating = $row['INDICE_VELOCIDAD *'] ?? $row['INDICE_VELOCIDAD (Obligatorio)'] ?? $row['INDICE_VELOCIDAD'] ?? 'N/A';
            $terrainTypeRaw = $row['TIPO_TERRENO'] ?? $row['TIPO_TERRENO (Opcional - H/T, A/T, M/T)'] ?? null;
            $terrainType = !empty($terrainTypeRaw) ? strtoupper(trim($terrainTypeRaw)) : null;
            $desc = $row['DESCRIPCION'] ?? $row['DESCRIPCION (Opcional)'] ?? null;
            
            $images = [];
            if (!empty($row['IMAGEN_1']) || !empty($row['IMAGEN_1 (Opcional)'])) $images[] = trim($row['IMAGEN_1'] ?? $row['IMAGEN_1 (Opcional)']);
            if (!empty($row['IMAGEN_2']) || !empty($row['IMAGEN_2 (Opcional)'])) $images[] = trim($row['IMAGEN_2'] ?? $row['IMAGEN_2 (Opcional)']);
            if (!empty($row['IMAGEN_3']) || !empty($row['IMAGEN_3 (Opcional)'])) $images[] = trim($row['IMAGEN_3'] ?? $row['IMAGEN_3 (Opcional)']);
            
            $runFlatRaw = $row['RUN_FLAT'] ?? $row['RUN_FLAT (Opcional - SI/NO)'] ?? 'NO';
            $runFlatVal = strtoupper(trim($runFlatRaw));
            $isRunFlat = in_array($runFlatVal, ['SI', 'SÍ', 'YES', '1']);

            $id = $row['ID'] ?? null;

            if (!empty($id)) {
                $tire = Tire::find($id);
                if (!$tire) {
                    $errors[] = "Fila $currentRow: El neumático con ID $id no existe.";
                    return;
                }
                $tire->update([
                    'brand_id' => $brand->id,
                    'category_id' => $categoryId,
                    'model' => $model,
                    'version' => $version,
                    'year' => $year,
                    'width' => $width,
                    'profile' => $profile,
                    'rim' => $rim,
                    'price' => $price,
                    'offer_price' => $offerPrice,
                    'stock' => $stock,
                    'load_index' => $loadIndex,
                    'speed_rating' => $speedRating,
                    'terrain_type' => $terrainType,
                    'is_run_flat' => $isRunFlat,
                    'description' => $desc,
                    'images_json' => !empty($images) ? $images : $tire->images_json
                ]);
            } else {
                Tire::create([
                    'brand_id' => $brand->id,
                    'category_id' => $categoryId,
                    'model' => $model,
                    'version' => $version,
                    'year' => $year,
                    'width' => $width,
                    'profile' => $profile,
                    'rim' => $rim,
                    'price' => $price,
                    'offer_price' => $offerPrice,
                    'stock' => $stock,
                    'load_index' => $loadIndex,
                    'speed_rating' => $speedRating,
                    'terrain_type' => $terrainType,
                    'is_run_flat' => $isRunFlat,
                    'description' => $desc,
                    'status' => true,
                    'is_promoted' => false,
                    'images_json' => $images
                ]);
            }
        });
        
        Cache::forget($cacheKey);
        Cache::forget('promotions.home');
        Cache::forget('brands.home');
        Cache::forget('tires.widths');
        Cache::forget('tires.profiles');
        Cache::forget('tires.rims');
        
        if (count($errors) > 0) {
            return redirect()->back()->with('error', 'Se importaron algunos registros, pero hubo errores: ' . implode(' | ', array_slice($errors, 0, 5)));
        }
        
        return redirect()->back()->with('success', 'Neumáticos importados correctamente.');
    }

    public function downloadTemplatePromotions()
    {
        $promotions = Tire::with(['brand'])->where('is_promo_only', true)->get();
        $data = [];

        foreach ($promotions as $promo) {
            $data[] = [
                'ID' => $promo->id,
                'REFERENCIA' => $promo->product_code ?? '',
                'MEDIDA' => $promo->measure_text,
                'DESCRIPCION' => $promo->model,
                'MARCA' => $promo->brand ? $promo->brand->name : '',
                'TIPO' => '', // Cannot easily extract from description, so leave blank or map if needed.
                'APLICACIÓN' => '', // Cannot easily extract, user can see original in description if needed
                'PRECIO' => $promo->offer_price ?? $promo->price,
                'STOCK' => $promo->stock
            ];
        }

        if (empty($data)) {
            $data[] = [
                'ID' => '',
                'REFERENCIA' => '',
                'MEDIDA' => '',
                'DESCRIPCION' => '',
                'MARCA' => '',
                'TIPO' => '',
                'APLICACIÓN' => '',
                'PRECIO' => '',
                'STOCK' => ''
            ];
        }

        $instructions = [
            ['CAMPO' => 'ID', 'EXPLICACIÓN' => 'IMPORTANTE: No modificar si se edita una promoción existente. Dejar vacío para crear una nueva.'],
            ['CAMPO' => 'Todos', 'EXPLICACIÓN' => 'Completa los campos. Estos neumáticos aparecerán SOLO en promociones.']
        ];

        $sheets = new \Rap2hpoutre\FastExcel\SheetCollection([
            'Instrucciones' => collect($instructions),
            'Plantilla Promociones' => collect($data)
        ]);

        return (new \Rap2hpoutre\FastExcel\FastExcel($sheets))->download('Plantilla_Promociones.xlsx');
    }

    public function importPromotions(Request $request)
    {
        $request->validate(['file' => 'required|file|mimes:xlsx,xls,csv,txt']);
        
        $filePath = $request->file('file')->getRealPath();
        
        $sheetIndex = 1;
        try {
            (new \Rap2hpoutre\FastExcel\FastExcel)->import($filePath, function ($row) use (&$sheetIndex) {
                if (isset($row['CAMPO']) || isset($row['EXPLICACIÓN'])) {
                    $sheetIndex = 2;
                }
                throw new \Exception("StopLoop");
            });
        } catch (\Exception $e) {}

        $totalRows = 0;
        (new \Rap2hpoutre\FastExcel\FastExcel)->sheet($sheetIndex)->import($filePath, function ($line) use (&$totalRows) {
            $totalRows++;
        });

        $cacheKey = 'import_progress_' . auth()->id();
        \Illuminate\Support\Facades\Cache::put($cacheKey, 0);

        $defaultCategory = Category::first();
        $defaultCategoryId = $defaultCategory ? $defaultCategory->id : 1;

        $currentRow = 0;
        $errors = [];
        (new \Rap2hpoutre\FastExcel\FastExcel)->sheet($sheetIndex)->import($filePath, function ($row) use ($totalRows, &$currentRow, $cacheKey, $defaultCategoryId, &$errors) {
            $currentRow++;
            if ($currentRow % 10 === 0 || $currentRow === $totalRows) {
                \Illuminate\Support\Facades\Cache::put($cacheKey, round(($currentRow / max(1, $totalRows)) * 100));
            }

            $id = $row['ID'] ?? null;
            $brandName = $row['MARCA'] ?? null;
            if (empty($brandName)) {
                $errors[] = "Fila $currentRow: La columna de Marca está vacía.";
                return;
            }

            $brand = Brand::firstOrCreate(
                ['name' => trim($brandName)],
                ['show_on_home' => true]
            );

            $measure = $row['MEDIDA'] ?? '';
            
            $parsedWidth = 0;
            $parsedProfile = 0;
            $parsedRim = 0;
            if (preg_match('/(\d{2,3})[^\d]+(\d{2,3})[^\d]+([\d\.]+)/', $measure, $matches)) {
                $parsedWidth = intval($matches[1]);
                $parsedProfile = intval($matches[2]);
                $parsedRim = intval($matches[3]);
            }

            $desc = $row['DESCRIPCION'] ?? 'Promo Tire';
            $price = floatval($row['PRECIO'] ?? 0);
            $stock = intval($row['STOCK'] ?? 10);
            
            $tipo = $row['TIPO'] ?? '';
            $aplicacion = $row['APLICACIÓN'] ?? '';
            $fullDesc = trim("$tipo $aplicacion") ?: $desc;

            if (!empty($id)) {
                $promoTire = Tire::find($id);
                if (!$promoTire) {
                    $errors[] = "Fila $currentRow: El neumático con ID $id no existe.";
                    return;
                }
                $promoTire->update([
                    'brand_id' => $brand->id,
                    'model' => $desc,
                    'width' => $parsedWidth,
                    'profile' => $parsedProfile,
                    'rim' => $parsedRim,
                    'description' => $fullDesc,
                    'price' => $price * 1.2,
                    'offer_price' => $price,
                    'stock' => $stock,
                    'measure_text' => $measure,
                ]);
            } else {
                Tire::create([
                    'brand_id' => $brand->id,
                    'category_id' => $defaultCategoryId,
                    'model' => $desc,
                    'width' => $parsedWidth,
                    'profile' => $parsedProfile,
                    'rim' => $parsedRim,
                    'load_index' => 0,
                    'speed_rating' => 'N/A',
                    'description' => $fullDesc,
                    'price' => $price * 1.2, // Simulate a 20% discount visually
                    'offer_price' => $price,
                    'stock' => $stock,
                    'status' => true,
                    'is_promoted' => true,
                    'is_promo_only' => true,
                    'measure_text' => $measure,
                ]);
            }
        });

        \Illuminate\Support\Facades\Cache::forget($cacheKey);
        \Illuminate\Support\Facades\Cache::forget('promotions.home');

        if (count($errors) > 0) {
            return redirect()->back()->with('error', 'Se importaron algunos registros, pero hubo errores: ' . implode(' | ', array_slice($errors, 0, 5)));
        }

        return redirect()->back()->with('success', 'Promociones importadas correctamente.');
    }

    public function importProgress()
    {
        $progress = Cache::get('import_progress_' . auth()->id(), null);
        return response()->json(['progress' => $progress]);
    }

    public function promotions(Request $request)
    {
        $query = Tire::with('brand')->where('is_promoted', true)->latest();

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

        $promotions = $query->paginate(15)->withQueryString();

        return Inertia::render('Admin/Promotions', [
            'promotions' => $promotions,
            'filters' => $request->only(['search'])
        ]);
    }

    public function searchTires(Request $request)
    {
        $query = Tire::with('brand')->where('is_promoted', false)->latest();

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

        return response()->json($query->take(20)->get());
    }

    public function togglePromotion(Tire $tire, Request $request)
    {
        $tire->update([
            'is_promoted' => $request->boolean('is_promoted')
        ]);

        return redirect()->back()->with('success', 'Estado de promoción actualizado.');
    }

    public function bulkDeleteInventory(Request $request)
    {
        $request->validate(['ids' => 'required|array']);
        Tire::whereIn('id', $request->ids)->delete();
        return redirect()->back()->with('success', 'Neumáticos eliminados correctamente.');
    }

    public function bulkPromote(Request $request)
    {
        $request->validate(['ids' => 'required|array']);
        Tire::whereIn('id', $request->ids)->update(['is_promoted' => true]);
        \Illuminate\Support\Facades\Cache::forget('promotions.home');
        return redirect()->back()->with('success', 'Neumáticos movidos a promociones correctamente.');
    }

    public function bulkDeletePromotions(Request $request)
    {
        $request->validate(['ids' => 'required|array']);
        Tire::whereIn('id', $request->ids)->delete();
        \Illuminate\Support\Facades\Cache::forget('promotions.home');
        return redirect()->back()->with('success', 'Promociones eliminadas correctamente.');
    }

    public function bulkDeleteBrands(Request $request)
    {
        $request->validate(['ids' => 'required|array']);
        $brands = Brand::whereIn('id', $request->ids)->get();
        foreach ($brands as $brand) {
            $brand->tires()->delete();
            $brand->delete();
        }
        \Illuminate\Support\Facades\Cache::forget('brands.home');
        return redirect()->back()->with('success', 'Marcas eliminadas correctamente.');
    }

    public function bulkDeleteCategories(Request $request)
    {
        $request->validate(['ids' => 'required|array']);
        $categories = Category::whereIn('id', $request->ids)->get();
        foreach ($categories as $category) {
            $category->tires()->delete();
            $category->delete();
        }
        return redirect()->back()->with('success', 'Categorías eliminadas correctamente.');
    }
}
