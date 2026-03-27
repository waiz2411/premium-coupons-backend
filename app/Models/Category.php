<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Category extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'name', 'description', 'icon', 'image_url'
    ];

    public function stores()
    {
        return $this->hasMany(Store::class);
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
