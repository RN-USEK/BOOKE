<?php

namespace App\Filament\Pages;

use App\Models\Book;
use App\Models\Category;
use App\Traits\HasBookInteractions;
use Filament\Pages\Page;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

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
        // dump($category);
        // dd($this);
        // $this->category = Category::findOrFail($category);
        $this->loadBooks();
        // dump($this->books);
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
}