<?php

namespace App\Filament\App\Pages;

use App\Models\Book;
use App\Models\BookInteraction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Notifications\Notification;
use Livewire\WithFileUploads;
use App\Services\GoogleBooksService;
use App\Services\GoogleVisionService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Models\Wishlist;
use App\Services\CartService;
use App\Models\Category;
use App\Services\WishlistService;
use Illuminate\Support\Facades\Session;
class Dashboard extends Page implements HasForms
{
    use InteractsWithForms;
    use WithFileUploads;

    protected static ?string $navigationIcon = 'heroicon-o-home';
    protected static ?string $navigationLabel = null;
    protected static ?string $slug = 'home'; // Add a unique slug
    protected static string $view = 'filament.app.pages.dashboard';
    protected static ?int $navigationSort = -2;
    protected static ?string $title = 'Home';

    public $searchQuery = '';
    public $imageUpload;
    public $searchResults = null;
    public $recommendedBooks = [];
    public $categories = [];
    public $popularBooks = [];


    public function mount(): void
    {
        $this->form->fill();
        $this->fetchRecommendations();
        $this->fetchCategories();
        $this->fetchPopularBooks();

    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('searchQuery')
                    ->placeholder('Enter search query'),
                FileUpload::make('imageUpload')
                    ->image()
                    ->maxSize(5120) // 5MB max
                    ->disk('public')
            ]);
    }

    public function search()
    {
        Log::info('Search function called', [
            'searchQuery' => $this->searchQuery,
            'imageUpload' => $this->imageUpload
        ]);
    
        if (empty($this->searchQuery) && empty($this->imageUpload)) {
            Log::warning('Search attempted with no query and no image');
            Notification::make()
                ->title('Search Error')
                ->body('Please provide either a search query or an image.')
                ->danger()
                ->send();
            return;
        }
    
        $googleBooksService = app(GoogleBooksService::class);
        $googleVisionService = app(GoogleVisionService::class);
    
        $searchQuery = $this->searchQuery ?? ''; // Ensure it's a string, even if empty
        Log::info('Initial search query', ['searchQuery' => $searchQuery]);
    
        if (!empty($this->imageUpload)) {
            Log::info('Image uploaded', ['imageUpload' => $this->imageUpload]);
            
            foreach ($this->imageUpload as $key => $uploadedFile) {
                if ($uploadedFile instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile) {
                    Log::info('Processing uploaded image', ['key' => $key, 'filename' => $uploadedFile->getFilename()]);
                    
                    $fullPath = $uploadedFile->getRealPath();
                    Log::info('Full path of uploaded image', ['fullPath' => $fullPath]);
                    
                    try {
                        Log::info('Calling Google Vision Service');
                        $detectedObjects = $googleVisionService->detectObjects($fullPath);
                        Log::info('Objects detected in image', ['detectedObjects' => $detectedObjects]);
                        $searchQuery .= ' ' . implode(' ', $detectedObjects);
                    } catch (\Exception $e) {
                        Log::error('Error detecting objects in image', ['error' => $e->getMessage()]);
                        Notification::make()
                            ->title('Image Processing Error')
                            ->body('There was an error processing the uploaded image. Please try again.')
                            ->danger()
                            ->send();
                        return;
                    }
                } else {
                    Log::warning('Unexpected file upload type', ['type' => gettype($uploadedFile)]);
                }
            }
            
            Log::info('Search query after adding detected objects', ['searchQuery' => $searchQuery]);
        }
    
        // Trim the search query and ensure it's not empty
        $searchQuery = trim($searchQuery);
        Log::info('Final search query after trimming', ['searchQuery' => $searchQuery]);
    
        if (empty($searchQuery)) {
            Log::warning('Search query is empty after processing');
            Notification::make()
                ->title('Search Error')
                ->body('No valid search terms found. Please try again.')
                ->danger()
                ->send();
            return;
        }
    
        Log::info('Calling Google Books Service');
        $this->searchResults = $googleBooksService->searchBooks($searchQuery);
        Log::info('Search results received', ['resultsCount' => count($this->searchResults)]);
    
        if (empty($this->searchResults)) {
            Log::info('No results found for search');
            Notification::make()
                ->title('No Results')
                ->body('No books found for your search.')
                ->warning()
                ->send();
        }
    }
    public function getBooks()
    {
        $user = Auth::user();
        $purchasedBookIds = $user->purchasedBooks()->pluck('id')->toArray();
        return Book::whereIn('id', $purchasedBookIds)->latest()->paginate(12);
    }
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
        if ($user) {
            return Wishlist::where('user_id', $user->id)
                ->where('book_id', $bookId)
                ->exists();
        }
        return false;
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

    private function generateRecommendationQuery()
    {
        $user = Auth::user();
        $topInteraction = BookInteraction::where('user_id', $user->id)
            ->orderBy('score', 'desc')
            ->first();

        if ($topInteraction) {
            $book = Book::find($topInteraction->book_id);
            if ($book && $book->category) {
                return $book->category->name;
            }
        }

        return 'popular books'; // Default query if no interactions or category found
    }

    public function fetchRecommendations()
    {
        $googleBooksService = app(GoogleBooksService::class);
        $query = $this->generateRecommendationQuery();

        Log::info('Fetching recommendations with query: ' . $query);

        $this->recommendedBooks = $googleBooksService->searchBooks($query);

        // Limit to 4 recommended books
        $this->recommendedBooks = array_slice($this->recommendedBooks, 0, 4);
    }
    public function fetchPopularBooks()
    {
        $googleBooksService = app(GoogleBooksService::class);
        $this->popularBooks = $googleBooksService->searchBooks('popular books');
        $this->popularBooks = array_slice($this->popularBooks, 0, 5);
    }

    public function fetchCategories()
    {
        $this->categories = Category::all();
    }
    ////////////// cart stuff

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
    public function getImageQueryWords()
    {
        $imageQueryWords = '';
        if (!empty($this->imageUpload)) {
            $googleVisionService = app(GoogleVisionService::class);
            foreach ($this->imageUpload as $key => $uploadedFile) {
                if ($uploadedFile instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile) {
                    $fullPath = $uploadedFile->getRealPath();
                    $detectedObjects = $googleVisionService->detectObjects($fullPath);
                    $imageQueryWords .= implode(', ', $detectedObjects) . ' ';
                }
            }
        }
        return trim($imageQueryWords);
    }
    public static function canAccess(): bool
    {
        $user = Auth::user();
        
        // Only allow access if the user has the 'user' role
        // and does not have 'admin' or 'manager' roles
        return $user && $user->hasRole('user') && !$user->hasAnyRole(['admin', 'manager']);
    }

}
