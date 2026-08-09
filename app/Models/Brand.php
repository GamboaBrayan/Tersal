<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'logo_url', 'show_on_home'];

    protected $appends = ['logo_full_url'];

    protected static function booted()
    {
        static::deleting(function ($brand) {
            if (!empty($brand->logo_url) && !str_starts_with($brand->logo_url, 'http')) {
                try {
                    \Illuminate\Support\Facades\Storage::disk('r2')->delete($brand->logo_url);
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error("Failed to delete brand logo from R2: {$brand->logo_url}", ['error' => $e->getMessage()]);
                }
            }
        });
    }

    public function tires()
    {
        return $this->hasMany(Tire::class);
    }

    protected function logoFullUrl(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(
            get: function () {
                if (!$this->logo_url) return null;
                if (str_starts_with($this->logo_url, 'http')) return $this->logo_url;
                return \Illuminate\Support\Facades\Storage::disk('r2')->url($this->logo_url);
            },
        );
    }
}
