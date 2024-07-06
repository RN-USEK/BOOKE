<?php

namespace App\Filament\Pages;

use App\Models\Book;
use Filament\Pages\Page;
use Filament\Actions\Action;

class ViewBook extends Page
{
    protected static bool $shouldRegisterNavigation = false; // This line prevents it from appearing in the menu

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.view-book';

    public function mount(Book $book): void
    {
        $this->book = $book;
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('favorite')
                ->icon('heroicon-o-heart')
                ->action(fn () => $this->toggleFavorite()),
            Action::make('add to cart')
                ->icon('heroicon-o-shopping-cart')
                ->action(fn () => $this->addToCart()),
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
    }

    private function addToCart(): void
    {
        // Implement add to cart logic here
    }
}

