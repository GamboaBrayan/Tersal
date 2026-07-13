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
        'brand_id', 'model', 'width', 'profile', 'rim', 
        'load_index', 'speed_rating', 'terrain_type', 'is_run_flat', 
        'description', 'price', 'offer_price', 'stock', 
        'images_json', 'status'
    ];

    protected $casts = [
        'is_run_flat' => 'boolean',
        'status' => 'boolean',
        'images_json' => 'array',
    ];

    protected $appends = ['has_discount', 'is_available'];

    public function brand()
    {
        return $this->belongsTo(Brand::class);
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
}
