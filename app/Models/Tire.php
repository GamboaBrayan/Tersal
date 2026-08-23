<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Tire extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'brand_id', 'category_id', 'model', 'year', 'version', 'width', 'profile', 'rim', 
        'load_index', 'speed_rating', 'terrain_type', 'is_run_flat', 
        'description', 'price', 'offer_price', 'stock', 
        'images_json', 'status', 'product_code', 'is_promoted'
    ];

    protected $casts = [
        'is_run_flat' => 'boolean',
        'status' => 'boolean',
        'images_json' => 'array',
        'price' => 'decimal:2',
        'offer_price' => 'decimal:2',
        'width' => 'float',
        'rim' => 'float',
    ];

    protected $appends = ['has_discount', 'is_available', 'image_urls'];

    protected static function booted()
    {
        static::created(function ($tire) {
            // Generate product code using ID padded with zeros (e.g. TT000001)
            $tire->product_code = 'TT' . str_pad($tire->id, 6, '0', STR_PAD_LEFT);
            $tire->saveQuietly();
        });

        static::saved(function ($tire) {
            \Illuminate\Support\Facades\Cache::forget('promotions.home');
            \Illuminate\Support\Facades\Cache::forget('tires.widths');
            \Illuminate\Support\Facades\Cache::forget('tires.profiles');
            \Illuminate\Support\Facades\Cache::forget('tires.rims');
            \Illuminate\Support\Facades\Cache::forget('tires.widths_active');
            \Illuminate\Support\Facades\Cache::forget('tires.profiles_active');
            \Illuminate\Support\Facades\Cache::forget('tires.rims_active');
        });

        static::deleted(function ($tire) {
            \Illuminate\Support\Facades\Cache::forget('promotions.home');
            \Illuminate\Support\Facades\Cache::forget('tires.widths');
            \Illuminate\Support\Facades\Cache::forget('tires.profiles');
            \Illuminate\Support\Facades\Cache::forget('tires.rims');
            \Illuminate\Support\Facades\Cache::forget('tires.widths_active');
            \Illuminate\Support\Facades\Cache::forget('tires.profiles_active');
            \Illuminate\Support\Facades\Cache::forget('tires.rims_active');
        });

        static::forceDeleted(function ($tire) {
            if (is_array($tire->images_json)) {
                foreach ($tire->images_json as $image) {
                    if (!empty($image) && !str_starts_with($image, 'http')) {
                        try {
                            \Illuminate\Support\Facades\Storage::disk('r2')->delete($image);
                        } catch (\Exception $e) {
                            \Illuminate\Support\Facades\Log::error("Failed to delete image from R2: {$image}", ['error' => $e->getMessage()]);
                        }
                    }
                }
            }
        });
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    protected function hasDiscount(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->offer_price !== null,
        );
    }

    protected function isAvailable(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->stock > 0,
        );
    }

    protected function imageUrls(): Attribute
    {
        return Attribute::make(
            get: function () {
                $urls = [];
                if (is_array($this->images_json)) {
                    foreach ($this->images_json as $path) {
                        if (str_starts_with($path, 'http')) {
                            $urls[] = $path;
                        } else {
                            $urls[] = \Illuminate\Support\Facades\Storage::disk('r2')->url($path);
                        }
                    }
                }
                return $urls;
            },
        );
    }
}
