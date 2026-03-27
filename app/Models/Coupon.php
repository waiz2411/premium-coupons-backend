<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Coupon extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'store_id', 'category_id', 'title', 'code', 'redirect_url', 'is_deal', 'discount', 'description', 'expiry_date', 'success_rate', 'is_verified', 'is_trending', 'is_new', 'countries'
    ];

    protected $casts = [
        'is_deal' => 'boolean',
        'is_verified' => 'boolean',
        'is_trending' => 'boolean',
        'is_new' => 'boolean',
        'countries' => 'array',
        'expiry_date' => 'date',
        'success_rate' => 'integer',
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
