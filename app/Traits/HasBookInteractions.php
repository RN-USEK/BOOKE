<?php

namespace App\Traits;

use App\Models\Book;
use App\Models\Wishlist;
use App\Services\CartService;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;

trait HasBookInteractions
{
    public function toggleWishlist($bookId)
    {
        $user = Auth::user();
        $wishlistItem = Wishlist::where('user_id', $user->id)
            ->where('book_id', $bookId)
            ->first();

        if ($wishlistItem) {
            $wishlistItem->delete();
            Notification::make()
                ->title('Removed from Wishlist')
                ->success()
                ->send();
        } else {
            Wishlist::create([
                'user_id' => $user->id,
                'book_id' => $bookId,
            ]);
            Notification::make()
                ->title('Added to Wishlist')
                ->success()
                ->send();
        }
    }

    public function isInWishlist($bookId)
    {
        $user = Auth::user();
        return Wishlist::where('user_id', $user->id)
            ->where('book_id', $bookId)
            ->exists();
    }

    public function addToCart($bookId)
    {
        $book = Book::findOrFail($bookId);
        CartService::add($book);
        
        Notification::make()
            ->title('Added to Cart')
            ->success()
            ->send();
    }

    public function removeFromCart($bookId)
    {
        CartService::remove($bookId);
        
        Notification::make()
            ->title('Removed from Cart')
            ->success()
            ->send();
    }

    public function updateCartQuantity($bookId, $quantity)
    {
        CartService::update($bookId, $quantity);
        
        Notification::make()
            ->title('Cart Updated')
            ->success()
            ->send();
    }

    public function getCartContent()
    {
        return CartService::getContent();
    }

    public function getCartTotal()
    {
        return CartService::getTotal();
    }
}