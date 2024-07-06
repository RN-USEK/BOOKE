<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Book;
use App\Models\Category;

class GoogleBooksService
{
    protected $apiKey;
    protected $baseUrl = 'https://www.googleapis.com/books/v1/volumes';

    public function __construct()
    {
        $this->apiKey = config('services.google.books_api_key');
    }

    public function searchBooks(string $query)
    {       $que = trim($query);
        if (empty($que)) {
            Log::warning('Empty query sent to Google Books API');
            return [];
        }
        $response = Http::get($this->baseUrl, [
            'q' => $query,
            'key' => $this->apiKey,
            // 'filter' => 'paid-ebooks',
            'printType' => 'books',
            'orderBy' => 'newest',
            'country' => 'US',
        ]);

        Log::info('Google Books API Response:', ['response' => $response->json()]);

        if ($response->successful()) {
            return $this->processAndSaveBooks($response->json()['items'] ?? []);
        }

        return [];
    }

    protected function processAndSaveBooks($items)
    {
        $savedBooks = [];
        foreach ($items as $item) {
            $bookData = $item['volumeInfo'] ?? [];
            $saleInfo = $item['saleInfo'] ?? [];

            Log::info('Processing book:', ['bookData' => $bookData, 'saleInfo' => $saleInfo]);

            if ($this->isValidBookData($bookData, $saleInfo)) {
                $category = $this->getOrCreateCategory($bookData['categories'][0] ?? 'Uncategorized');

                $bookToSave = [
                    'isbn' => $bookData['industryIdentifiers'][0]['identifier'] ?? $item['id'] ?? null,
                    'title' => $bookData['title'],
                    'author' => implode(', ', $bookData['authors']),
                    'description' => $bookData['description'] ?? 'No description available',
                    'price' => $saleInfo['listPrice']['amount'],
                    'stock' => 10,
                    'cover_image' => $bookData['imageLinks']['thumbnail'] ?? null,
                    'category_id' => $category->id,
                ];

                Log::info('Saving book:', $bookToSave);

                $book = Book::updateOrCreate(
                    ['isbn' => $bookToSave['isbn']],
                    $bookToSave
                );

                $savedBooks[] = $book;
            } else {
                Log::warning('Skipped book due to missing required fields:', ['bookData' => $bookData, 'saleInfo' => $saleInfo]);
            }
        }
        return $savedBooks;
    }

    protected function isValidBookData($bookData, $saleInfo)
    {
        return !empty($bookData['title']) && 
               !empty($bookData['authors']) && 
               !empty($bookData['description']) &&
               isset($saleInfo['listPrice']['amount']);
    }

    protected function getOrCreateCategory($categoryName)
    {
        return Category::firstOrCreate(['name' => $categoryName]);
    }
}
