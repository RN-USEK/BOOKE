<?php

namespace App\Filament\Pages;

use App\Models\Book;
use Filament\Pages\Page;
use Filament\Actions\Action;
use Illuminate\Support\Facades\Log;

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
                ->icon('heroicon-o-heart')
                ->action(function () {
                    $this->toggleFavorite();
                    return '';  // Return an empty string instead of null
                }),
            Action::make('add to cart')
                ->icon('heroicon-o-shopping-cart')
                ->action(function () {
                    $this->addToCart();
                    return '';  // Return an empty string instead of null
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

    private function toggleFavorite(): void
    {
        // Implement favorite logic here
        Log::info('Toggle favorite called');
    }

    private function addToCart(): void
    {
        // Implement add to cart logic here
        Log::info('Add to cart called');
    }
}