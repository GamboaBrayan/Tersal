<?php

use App\Http\Controllers\CatalogController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/login', function () {
    return redirect()->route('admin.login');
})->name('login');
Route::get('/catalog', [CatalogController::class, 'index'])->name('catalog');
Route::get('/catalog/{tire}', [CatalogController::class, 'show'])->name('catalog.show');
Route::get('/guide', function() {
    $faqsSetting = \App\Models\Setting::where('key', 'faqs')->first();
    $faqs = $faqsSetting ? json_decode($faqsSetting->value, true) : [];
    return Inertia::render('Guide/Index', [
        'faqs' => $faqs
    ]);
})->name('guide');

Route::get('/contacto', function() {
    return Inertia::render('Contact/Index');
})->name('contacto');

Route::get('/legales', function() {
    return Inertia::render('Legal/Terms');
})->name('legales');

Route::get('/privacidad', function() {
    return Inertia::render('Legal/Privacy');
})->name('privacidad');

use App\Http\Controllers\AdminAuthController;

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/login', [AdminAuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AdminAuthController::class, 'login'])->name('login.post');
    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');

    Route::middleware('auth')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        
        // Inventory
        Route::get('/inventory', [DashboardController::class, 'inventory'])->name('inventory');
        Route::post('/inventory', [DashboardController::class, 'store'])->name('inventory.store');
        Route::get('/inventory/create', [DashboardController::class, 'create'])->name('inventory.create');
        Route::get('/inventory/{tire}/edit', [DashboardController::class, 'edit'])->name('inventory.edit');
        Route::put('/inventory/{tire}', [DashboardController::class, 'update'])->name('inventory.update');
        Route::delete('/inventory/{tire}', [DashboardController::class, 'destroy'])->name('inventory.destroy');
        
        // Settings
        Route::get('/settings', [DashboardController::class, 'settings'])->name('settings');
        Route::post('/settings', [DashboardController::class, 'updateSettings'])->name('settings.update');

        // Brands
        Route::get('/brands', [DashboardController::class, 'brands'])->name('brands');
        Route::post('/brands', [DashboardController::class, 'storeBrand'])->name('brands.store');
        Route::post('/brands/{brand}', [DashboardController::class, 'updateBrand'])->name('brands.update');
        Route::delete('/brands/{brand}', [DashboardController::class, 'destroyBrand'])->name('brands.destroy');
    });
});
