<?php

namespace App\Services;

use App\Models\Wishlist;
use Illuminate\Support\Facades\Auth;

class WishlistService
{
    public static function isInWishlist($bookId)
    {
        $user = Auth::user();
        return Wishlist::where('user_id', $user->id)
            ->where('book_id', $bookId)
            ->exists();
    }

    public static function toggleWishlist($bookId)
    {
        $user = Auth::user();
        $wishlistItem = Wishlist::where('user_id', $user->id)
            ->where('book_id', $bookId)
            ->first();

        if ($wishlistItem) {
            $wishlistItem->delete();
        } else {
            Wishlist::create([
                'user_id' => $user->id,
                'book_id' => $bookId,
            ]);
        }
    }
}