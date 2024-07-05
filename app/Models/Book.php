<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Book extends Model
{
    use HasFactory;
    protected $fillable = [
        'title', 'author', 'description', 'isbn', 'price', 'stock', 'cover_image', 'category_id'
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);

    }
}
