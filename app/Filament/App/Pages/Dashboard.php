<?php

namespace App\Filament\App\Pages;
use App\Models\Book;
use Filament\Pages\Page;
use Filament\Actions\Action;
use Filament\Support\Enums\ActionSize;

class Dashboard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.app.pages.dashboard';
    protected static ?string $title = 'Dashboard';

    protected static ?int $navigationSort = -2;

    public function getBooks()
    {
        return Book::with('category')->latest()->take(12)->get();
    }

    // protected function getHeaderActions(): array
    // {
    //     return [
    //         Action::make('view all books')
    //             ->url(route('filament.admin.resources.books.index'))
    //             ->size(ActionSize::Small),
    //     ];
    // }
}
