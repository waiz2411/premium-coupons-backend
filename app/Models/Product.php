<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Product extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'store_id', 'category_id', 'title', 'original_price', 'discounted_price', 'description', 'affiliate_url', 'image_url', 'is_trending', 'countries'
    ];

    protected $casts = [
        'is_trending' => 'boolean',
        'countries' => 'array',
    ];

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
