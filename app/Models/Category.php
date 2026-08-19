<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'order'
    ];

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::addGlobalScope('order', function (Builder $builder) {
            $builder->orderBy('order', 'asc');
        });

        static::saved(function ($category) {
            \Illuminate\Support\Facades\Cache::forget('categories.all');
        });

        static::deleted(function ($category) {
            \Illuminate\Support\Facades\Cache::forget('categories.all');
        });
    }

    public function tires()
    {
        return $this->hasMany(Tire::class);
    }
}
