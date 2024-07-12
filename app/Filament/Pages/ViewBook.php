<?php

namespace App\Filament\Pages;

use App\Models\Book;
use Filament\Pages\Page;
use Filament\Actions\Action;
use Illuminate\Support\Facades\Log;
use App\Models\Wishlist;
use Illuminate\Support\Facades\Auth;
use Filament\Support\Colors\Color;
use Illuminate\Support\Facades\Session;
use Filament\Forms\Concerns\InteractsWithForms;
use App\Services\BookInteractionService;
use App\Services\WishlistService;
use Filament\Notifications\Notification;

class ViewBook extends Page
{
    use InteractsWithForms;
    
    public $rating = 5;
    public $comment = '';
    public $userReview;

    protected static string $view = 'filament.pages.view-book';
    
    protected static bool $shouldRegisterNavigation = false;

    public ?Book $record = null;

    public function mount(): void
    {
        $recordId = request()->query('recordId');
        Log::info('RecordId: ' . $recordId);
        if ($recordId) {
            $this->record = Book::findOrFail($recordId);
            Log::info('Record: ' . json_encode($this->record));
            $bookInteractionService = app()->make(BookInteractionService::class);

            $bookInteractionService->recordInteraction($this->record->id, 'view');

        }
    }
    
    protected function getHeaderActions(): array
    {
        return [
            Action::make('favorite')
                ->icon(fn () => $this->isInViewBookWishList() ? 'heroicon-s-heart' : 'heroicon-o-heart')
                ->color(fn () => $this->isInViewBookWishList() ? Color::Red : Color::Gray)
                ->label(fn () => $this->isInViewBookWishList() ? 'Remove from Wishlist' : 'Add to Wishlist')
                ->action(function () {
                    $this->toggleWishlist();
                    return '';
                }),
            Action::make('add to cart')
                ->icon(fn () => $this->isInCart() ? 'heroicon-s-shopping-cart' : 'heroicon-o-shopping-cart')
                ->color(fn () => $this->isInCart() ? Color::Green : Color::Gray)
                ->label(fn () => $this->isInCart() ? 'Remove from Cart' : 'Add to Cart')
                ->action(function () {
                    $this->toggleCart();
                    return '';
                }),
        ];
    }
    
    protected function getFooterActions(): array
    {
        return [
            Action::make('go back')
                ->icon('heroicon-o-arrow-left')
                ->action(function () {
                    return $this->back();
                }),
        ];
    }

    public function isInViewBookWishlist()
    {
        if ($this->record) {
            return WishlistService::isInWishlist($this->record->id);
        }
        return false;
        }

    public function toggleWishlist()
    {
        WishlistService::toggleWishlist($this->record->id);
    }

    public function toggleCart()
    {
        $cart = Session::get('cart', []);

        if (isset($cart[$this->record->id])) {
            unset($cart[$this->record->id]);
        } else {
            $cart[$this->record->id] = [
                'title' => $this->record->title,
                'price' => $this->record->price,
                'quantity' => 1,
            ];
        }

        Session::put('cart', $cart);
        $this->dispatch('cart-updated');
    }
    public function isInCart()
    {
        if ($this->record) {
            $cart = Session::get('cart', []);
            return isset($cart[$this->record->id]);
        }
        return false;
    }

    public function getCartContent()
    {
        return Session::get('cart', []);
    }

    public function getCartTotal()
    {
        $cart = Session::get('cart', []);
        return array_sum(array_map(function($item) {
            return $item['price'] * $item['quantity'];
        }, $cart));
    }

    public function updateCartQuantity($bookId, $quantity)
    {
        $cart = Session::get('cart', []);
        if (isset($cart[$bookId])) {
            if ($quantity > 0) {
                $cart[$bookId]['quantity'] = $quantity;
            } else {
                unset($cart[$bookId]);
            }
            Session::put('cart', $cart);
        }
    }

    public function removeFromCart($bookId)
    {
        $cart = Session::get('cart', []);
        if (isset($cart[$bookId])) {
            unset($cart[$bookId]);
            Session::put('cart', $cart);
        }
    }
        public function proceedToCheckout()
    {
        return redirect()->route('filament.app.pages.checkout');
    }

    ////////////////////////review stuff
    public function isBookPurchased()
    {
        $user = Auth::user();
        $purchasedBooks = $user->purchasedBooks();
        return $purchasedBooks->contains(function ($book) {
            return $book->id == $this->record->id;
        });
    }
    public function hasUserReviewed()
    {
        if ($this->isBookPurchased()) {
            $user = Auth::user();
            return $this->record->reviews()->where('user_id', $user->id)->count() > 0;
        }
        return false;
    }
    
    public function getUserReview()
    {
        if ($this->isBookPurchased()) {
            $user = Auth::user();
            return $this->record->reviews()->where('user_id', $user->id)->first();
        }
        return null;
    }
    
    public function setRating($rating)
    {
        if ($this->isBookPurchased()) {
            $this->rating = $rating;
        } else {
            $this->rating = null;
        }
    }

public function submitReview()
{
    $this->validate([
        'rating' => 'required|integer|between:1,5',
        'comment' => 'nullable|string|max:65535',
    ]);

    $review = Review::create([
        'user_id' => Auth::id(),
        'book_id' => $this->record->id,
        'rating' => $this->rating,
        'comment' => $this->comment,
    ]);

    $this->userReview = $review;
    $this->comment = '';
    $this->rating = 5;

    Notification::make()
    ->title('Review Submitted')
    ->success()
    ->send();
}

public function updateReview()
{
    $this->validate([
        'rating' => 'required|integer|between:1,5',
        'comment' => 'nullable|string|max:65535',
    ]);

    $this->userReview->update([
        'rating' => $this->rating,
        'comment' => $this->comment,
    ]);

    Notification::make()
    ->title('Review Updated')
    ->success()
    ->send();
   }
}