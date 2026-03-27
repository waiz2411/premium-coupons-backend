<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    use HasFactory;

    // The primary key associated with the table.
    protected $primaryKey = 'code';
    
    // Indicates if the IDs are auto-incrementing.
    public $incrementing = false;
    
    // The "type" of the primary key ID.
    protected $keyType = 'string';

    protected $fillable = [
        'code', 'name', 'hero_images', 'featured_store_ids', 'trending_coupon_ids'
    ];

    protected $casts = [
        'hero_images' => 'array',
        'featured_store_ids' => 'array',
        'trending_coupon_ids' => 'array',
    ];
}
