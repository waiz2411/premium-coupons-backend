<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class CustomPage extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'name', 'slug', 'hero_title', 'hero_description', 'hero_image', 'selected_coupon_ids', 'is_subpage', 'parent_page_id'
    ];

    protected $casts = [
        'selected_coupon_ids' => 'array',
        'is_subpage' => 'boolean',
    ];

    public function parent()
    {
        return $this->belongsTo(CustomPage::class, 'parent_page_id');
    }

    public function subpages()
    {
        return $this->hasMany(CustomPage::class, 'parent_page_id');
    }
}
