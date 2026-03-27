<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SeoSetting extends Model
{
    use HasFactory;

    protected $primaryKey = 'page_key';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'page_key', 'meta_title', 'meta_description', 'keywords', 'url_slug'
    ];
}
