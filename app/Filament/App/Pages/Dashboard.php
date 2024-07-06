<?php

namespace App\Filament\App\Pages;

use App\Models\Book;
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
        return $this->searchResults ?? Book::with('category')->latest()->take(12)->get();
    }
}