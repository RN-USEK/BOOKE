<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Book;
use App\Models\BookInteraction;
use Illuminate\Support\Facades\Storage;

class ExportDataToCSV extends Command
{
    protected $signature = 'export:csv';
    protected $description = 'Export books and interactions data to CSV files';

    public function handle()
    {
        $this->exportBooks();
        $this->exportInteractions();
        $this->info('Data exported to CSV files successfully.');
    }

    private function exportBooks()
    {
        $books = Book::with('category')->get();
        $csvContent = "id,title,author,description,category,isbn,price,rating,in_stock\n";

        foreach ($books as $book) {
            $csvContent .= implode(',', [
                $book->id,
                '"' . str_replace('"', '""', $book->title) . '"',
                '"' . str_replace('"', '""', $book->author) . '"',
                '"' . str_replace('"', '""', $book->description) . '"',
                '"' . str_replace('"', '""', $book->category->name) . '"',
                $book->isbn,
                $book->price,
                $book->averageRating(),
                $book->stock > 0 ? 'true' : 'false'
            ]) . "\n";
        }

        Storage::disk('local')->put('books_export.csv', $csvContent);
    }

    private function exportInteractions()
    {
        $interactions = BookInteraction::all();
        $csvContent = "user_id,book_id,interaction_type,score,timestamp\n";

        foreach ($interactions as $interaction) {
            $csvContent .= implode(',', [
                $interaction->user_id,
                $interaction->book_id,
                $interaction->interaction_type,
                $interaction->score,
                $interaction->created_at->format('Y-m-d H:i:s')
            ]) . "\n";
        }

        Storage::disk('local')->put('interactions_export.csv', $csvContent);
    }
}