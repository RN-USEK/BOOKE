<?php

namespace App\Filament\Pages;

use App\Models\Book;
use App\Models\Review;
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
use Illuminate\Support\Facades\Redirect;
class ViewBook extends Page
{
    use InteractsWithForms;
    
    public $rating;
    public $comment;
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

            $this->loadUserReview();
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
                ->url(url()->previous())
                ->openUrlInNewTab(false),
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
    public function loadUserReview()
    {
        $this->userReview = $this->getUserReview();
        if ($this->userReview) {
            $this->rating = $this->userReview->rating;
            $this->comment = $this->userReview->comment;
            Log::info('Loaded user review', [
                'rating' => $this->rating,
                'comment' => $this->comment,
            ]);
        } else {
            Log::info('No existing user review found');
        }
    }

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
            Log::info('setRating() called with rating: ' . $rating);
            $this->rating = $rating;
            
            if ($this->userReview) {
                $updated = $this->userReview->update(['rating' => $this->rating]);
                Log::info('Rating update result', ['updated' => $updated, 'new_rating' => $this->rating]);
            }
            
            Log::info('Rating set', ['rating' => $this->rating]);
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

        Log::info('Submitting new review', [
            'rating' => $this->rating,
            'comment' => $this->comment,
        ]);

        $review = Review::create([
            'user_id' => Auth::id(),
            'book_id' => $this->record->id,
            'rating' => $this->rating,
            'comment' => $this->comment,
        ]);

        $this->userReview = $review;
        $this->loadUserReview();

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
    
        Log::info('Updating existing review', [
            'current_rating' => $this->rating,
            'new_rating' => $this->rating,
            'comment' => $this->comment,
            'review_id' => $this->userReview->id,
        ]);
    
        try {
            $updated = $this->userReview->update([
                'rating' => $this->rating,
                'comment' => $this->comment,
            ]);
    
            Log::info('Review update result', [
                'updated' => $updated,
                'new_rating' => $this->userReview->fresh()->rating,
                'new_comment' => $this->userReview->fresh()->comment,
            ]);
    
            if ($updated) {
                $this->loadUserReview();
                Notification::make()
                    ->title('Review Updated')
                    ->success()
                    ->send();
            } else {
                throw new \Exception('Update operation returned false');
            }
        } catch (\Exception $e) {
            Log::error('Failed to update review', [
                'review_id' => $this->userReview->id,
                'rating' => $this->rating,
                'comment' => $this->comment,
                'error' => $e->getMessage(),
            ]);
            Notification::make()
                ->title('Failed to update review')
                ->danger()
                ->send();
        }
    }
}