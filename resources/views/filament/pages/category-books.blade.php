<x-filament-panels::page class="bg-gray-100 dark:bg-gray-900">
    <style>
        .container {
            max-width: 1200px;
            margin-left: auto;
            margin-right: auto;
            padding-left: 1rem;
            padding-right: 1rem;
        }
        .book-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
            gap: 1rem;
        }
        @media (min-width: 640px) {
            .book-grid {
                grid-template-columns: repeat(3, minmax(160px, 1fr));
            }
        }
        @media (min-width: 768px) {
            .book-grid {
                grid-template-columns: repeat(4, minmax(160px, 1fr));
            }
        }
        @media (min-width: 1024px) {
            .book-grid {
                grid-template-columns: repeat(5, minmax(160px, 1fr));
            }
        }
    </style>

    <div class="container">
        <h1 class="text-2xl font-bold mb-4">{{ $this->category->name }} Books</h1>

        <div class="book-grid mb-8">
            @foreach($this->books as $book)
                @include('partials.book-card', ['book' => $book])
            @endforeach
        </div>

    </div>
</x-filament-panels::page>