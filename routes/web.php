<?php

use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\ComplaintController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\VehicleSearchController;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::redirect('/login', '/admin/login')->name('login');
Route::get('/catalog', [CatalogController::class, 'index'])->name('catalog');
Route::get('/catalog/{tire}', [CatalogController::class, 'show'])->name('catalog.show');
Route::get('/guide', [PageController::class, 'guide'])->name('guide');
Route::get('/contacto', [PageController::class, 'contact'])->name('contacto');
Route::get('/sobre-nosotros', [PageController::class, 'about'])->name('sobre-nosotros');
// Route::get('/legales', [PageController::class, 'terms'])->name('legales');
// Route::get('/privacidad', [PageController::class, 'privacy'])->name('privacidad');
Route::get('/libro-reclamaciones', [PageController::class, 'complaintsBook'])->name('libro-reclamaciones');
Route::post('/libro-reclamaciones', [ComplaintController::class, 'store'])->middleware('throttle:3,1')->name('libro-reclamaciones.store');

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/login', [AdminAuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AdminAuthController::class, 'login'])->middleware('throttle:5,1')->name('login.post');
    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');

    Route::middleware('auth')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        
        // Inventory
        Route::get('/inventory', [DashboardController::class, 'inventory'])->name('inventory');
        Route::get('/inventory/template', [DashboardController::class, 'downloadTemplateInventory'])->name('inventory.template');
        Route::post('/inventory/import', [DashboardController::class, 'importInventory'])->name('inventory.import');
        Route::post('/inventory/bulk-delete', [DashboardController::class, 'bulkDeleteInventory'])->name('inventory.bulk-delete');
        Route::post('/inventory/bulk-promote', [DashboardController::class, 'bulkPromote'])->name('inventory.bulk-promote');
        Route::get('/import-progress', [DashboardController::class, 'importProgress'])->name('import.progress');
        Route::post('/inventory', [DashboardController::class, 'store'])->name('inventory.store');
        Route::get('/inventory/create', [DashboardController::class, 'create'])->name('inventory.create');
        Route::get('/inventory/{tire}/edit', [DashboardController::class, 'edit'])->name('inventory.edit');
        Route::put('/inventory/{tire}', [DashboardController::class, 'update'])->name('inventory.update');
        Route::delete('/inventory/{tire}', [DashboardController::class, 'destroy'])->name('inventory.destroy');
        
        // Promotions
        Route::get('/promotions', [DashboardController::class, 'promotions'])->name('promotions');
        Route::get('/promotions/search', [DashboardController::class, 'searchTires'])->name('promotions.search');
        Route::post('/promotions/{tire}/toggle', [DashboardController::class, 'togglePromotion'])->name('promotions.toggle');
        Route::get('/inventory/template-promotions', [DashboardController::class, 'downloadTemplatePromotions'])->name('inventory.template-promotions');
        Route::post('/inventory/import-promotions', [DashboardController::class, 'importPromotions'])->name('inventory.import-promotions');
        Route::post('/promotions/bulk-delete', [DashboardController::class, 'bulkDeletePromotions'])->name('promotions.bulk-delete');

        // Settings
        Route::get('/settings', [DashboardController::class, 'settings'])->name('settings');
        Route::post('/settings', [DashboardController::class, 'updateSettings'])->name('settings.update');

        // Brands
        Route::get('/brands', [DashboardController::class, 'brands'])->name('brands');
        Route::get('/brands/template', [DashboardController::class, 'downloadTemplateBrands'])->name('brands.template');
        Route::post('/brands/import', [DashboardController::class, 'importBrands'])->name('brands.import');
        Route::post('/brands/bulk-delete', [DashboardController::class, 'bulkDeleteBrands'])->name('brands.bulk-delete');
        Route::post('/brands', [DashboardController::class, 'storeBrand'])->name('brands.store');
        Route::post('/brands/{brand}', [DashboardController::class, 'updateBrand'])->name('brands.update');
        Route::delete('/brands/{brand}', [DashboardController::class, 'destroyBrand'])->name('brands.destroy');

        // Categories
        Route::get('/categories', [DashboardController::class, 'categories'])->name('categories');
        Route::post('/categories/bulk-delete', [DashboardController::class, 'bulkDeleteCategories'])->name('categories.bulk-delete');
        Route::post('/categories', [DashboardController::class, 'storeCategory'])->name('categories.store');
        Route::post('/categories/{category}/move', [DashboardController::class, 'moveCategory'])->name('categories.move');
        Route::post('/categories/{category}', [DashboardController::class, 'updateCategory'])->name('categories.update');
        Route::delete('/categories/{category}', [DashboardController::class, 'destroyCategory'])->name('categories.destroy');
    });
});

Route::prefix('api/vehicles')->middleware('throttle:30,1')->group(function () {
    Route::get('/makes', [VehicleSearchController::class, 'getMakes']);
    Route::get('/models', [VehicleSearchController::class, 'getModels']);
    Route::get('/years', [VehicleSearchController::class, 'getYears']);
    Route::get('/trims', [VehicleSearchController::class, 'getTrims']);
});

Route::post('/api/deploy/migrate', function (Request $request) {
    $secret = env('DEPLOY_SECRET');
    if (!$secret || !hash_equals($secret, (string) $request->header('X-Deploy-Token'))) {
        abort(403, 'No autorizado');
    }

    Artisan::call('migrate', ['--force' => true]);
    $migrateOutput = Artisan::output();

    Artisan::call('optimize:clear');

    return response()->json([
        'ok' => true,
        'output' => trim($migrateOutput),
    ]);
})->withoutMiddleware([ValidateCsrfToken::class]);

Route::fallback([PageController::class, 'fallback']);


