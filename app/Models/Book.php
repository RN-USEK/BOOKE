<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

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
    public function wishlistedBy(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'wishlists')->withTimestamps();
    }
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function averageRating()
    {
        return $this->reviews()->avg('rating');
    }
    public function interactions()
    {
        return $this->hasMany(BookInteraction::class);
    }
}
