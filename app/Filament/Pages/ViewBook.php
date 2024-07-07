<?php

namespace App\Filament\Pages;

use App\Models\Book;
use Filament\Pages\Page;
use Filament\Actions\Action;
use Illuminate\Support\Facades\Log;
use App\Models\Wishlist;
use Illuminate\Support\Facades\Auth;
use Filament\Support\Colors\Color;
class ViewBook extends Page
{
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
            // Action::make('add to cart')
            //     ->icon(fn () => $this->isInCart() ? 'heroicon-s-shopping-cart' : 'heroicon-o-shopping-cart')
            //     ->color(fn () => $this->isInCart() ? Color::Green : Color::Gray)
            //     ->label(fn () => $this->isInCart() ? 'Remove from Cart' : 'Add to Cart')
            //     ->action(function () {
            //         $this->toggleCart();
            //         return '';
            //     }),
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
        }
    }

    public function isInWishlist()
    {
        $user = Auth::user();
        return Wishlist::where('user_id', $user->id)
            ->where('book_id', $this->record->id)
            ->exists();
    }

    private function addToCart(): void
    {
        // Implement add to cart logic here
        Log::info('Add to cart called');
    }
}