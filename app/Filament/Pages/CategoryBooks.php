<?php

namespace App\Filament\Pages;

use App\Models\Book;
use App\Models\Category;
use App\Traits\HasBookInteractions;
use Filament\Pages\Page;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class CategoryBooks extends Page
{
    use HasBookInteractions;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.category-books';
    protected static ?string $slug = 'app/category/{category}';
    protected static bool $shouldRegisterNavigation = false;

    public Category $category;
    public Collection $books;

    public function mount($category)
    {
        $this->loadBooks();
    }

    public function loadBooks()
    {
        // Log the SQL query
        Log::debug('Executing query to fetch books for category:', [
            'category_id' => $this->category->id,
        ]);
    
        $this->books = $this->category->books()->get();
        // dd($this->books);

        // Log the result
        Log::debug('Books fetched for category:', [
            'books' => $this->books,
        ]);
    }
    protected function getViewData(): array
    {
        return [
            'books' => $this->books,
        ];
    }
    protected static function getRoutes(): array
    {
        return [
            static::$slug => static::class,
        ];
    }
     // cart functions

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
 
     public function proceedToCheckout()
     {
         return redirect()->route('filament.app.pages.checkout');
     }
}