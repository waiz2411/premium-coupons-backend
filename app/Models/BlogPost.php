<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class BlogPost extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'title', 'excerpt', 'content', 'image', 'author', 'date', 'category', 'read_time'
    ];

    protected $casts = [
        'date' => 'date',
    ];
}
