<?php

namespace App\Filament\App\Pages;

use App\Models\Book;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Livewire\WithFileUploads;
use App\Services\GoogleBooksService;
use App\Services\GoogleVisionService;

class Dashboard extends Page implements HasForms
{
    use InteractsWithForms;
    use WithFileUploads;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static string $view = 'filament.app.pages.dashboard';
    protected static ?string $title = 'Dashboard';
    protected static ?int $navigationSort = -2;

    public $searchQuery = '';
    public $imageUpload;
    public $searchResults = null;

    public function mount(): void
    {
        $this->form->fill();
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
            ]);
    }

    public function search()
    {
        $this->validate([
            'searchQuery' => 'required|min:2',
        ]);

        $googleBooksService = app(GoogleBooksService::class);
        $googleVisionService = app(GoogleVisionService::class);

        $searchQuery = $this->searchQuery;

        if ($this->imageUpload) {
            $imagePath = $this->imageUpload->getRealPath();
            $detectedObjects = $googleVisionService->detectObjects($imagePath);
            $searchQuery .= ' ' . implode(' ', $detectedObjects);
        }

        $this->searchResults = $googleBooksService->searchBooks($searchQuery);
    }

    public function getBooks()
    {
        return $this->searchResults ?? Book::with('category')->latest()->take(12)->get();
    }
}

    // protected function getHeaderActions(): array
    // {
    //     return [
    //         Action::make('view all books')
    //             ->url(route('filament.admin.resources.books.index'))
    //             ->size(ActionSize::Small),
    //     ];
    // }

