<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Store extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'name', 'logo', 'description', 'about', 'category_id', 'rating', 'is_featured', 'affiliate_link', 'countries'
    ];

    protected $casts = [
        'countries' => 'array',
        'is_featured' => 'boolean',
        'rating' => 'float',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function coupons()
    {
        return $this->hasMany(Coupon::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
