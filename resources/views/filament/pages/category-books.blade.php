<x-filament-panels::page>
    <h1 class="text-2xl font-bold mb-4">{{ $this->category->name }} Books</h1>

    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
        @foreach($this->books as $book)
            @include('partials.book-card', ['book' => $book])
        @endforeach
    </div>
</x-filament-panels::page>