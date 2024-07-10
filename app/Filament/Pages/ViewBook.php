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
class ViewBook extends Page
{
    use InteractsWithForms;
    
    public $rating;
    public $comment;
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
                ->icon(fn () => $this->isInWishlist() ? 'heroicon-s-heart' : 'heroicon-o-heart')
                ->color(fn () => $this->isInWishlist() ? Color::Red : Color::Gray)
                ->label(fn () => $this->isInWishlist() ? 'Remove from Wishlist' : 'Add to Wishlist')
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
                ->url(fn (): string => route('filament.app.pages.dashboard')),
        ];
    }

    public function toggleWishlist()
    {
        $user = Auth::user();
        $wishlistItem = Wishlist::where('user_id', $user->id)
            ->where('book_id', $this->record->id)
            ->first();

        if ($wishlistItem) {
            $wishlistItem->delete();
        } else {
            Wishlist::create([
                'user_id' => $user->id,
                'book_id' => $this->record->id,
            ]);
            app(BookInteractionService::class)->recordInteraction($this->record->id, 'wishlist');

        }
    }

    public function isInWishlist()
    {
        $user = Auth::user();
        return Wishlist::where('user_id', $user->id)
            ->where('book_id', $this->record->id)
            ->exists();
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
        $cart = Session::get('cart', []);
        return isset($cart[$this->record->id]);
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
   }