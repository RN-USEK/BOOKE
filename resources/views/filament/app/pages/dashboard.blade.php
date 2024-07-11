<x-filament-panels::page>
    <div class="hero-image bg-sky-500 w-full h-64 flex items-center justify-center">
        <h1 class="text-white text-4xl font-bold">Welcome to Your Bookstore</h1>
    </div>
    
    <form wire:submit.prevent="search" class="mb-6 mt-4">
        {{ $this->form }}
        <x-filament::button type="submit" class="mt-4">
            Search
        </x-filament::button>
    </form>
    
    <div class="categories mb-6">
        <h2 class="text-2xl font-bold mb-4">Categories</h2>
        <div class="flex flex-wrap gap-2">
            @php
                $colors = ['red', 'blue', 'green', 'yellow', 'purple', 'pink', 'indigo', 'teal'];
            @endphp
            @foreach($this->categories as $category)
                @php
                    $color = $colors[array_rand($colors)];
                @endphp
                <button class="bg-{{ $color }}-500 text-white py-2 px-4 rounded-lg">
                    {{ $category->name }}
                </button>
            @endforeach
        </div>
    </div>

    @if($this->searchResults !== null)
        <h2 class="text-2xl font-bold mb-4">Your Results</h2>
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 mb-8">
            @foreach($this->searchResults as $book)
                @include('partials.book-card', ['book' => $book, 'height' => 'short'])
            @endforeach
        </div>
    @endif

    <h2 class="text-2xl font-bold mb-4">For You</h2>
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 mb-8">
        @foreach($this->recommendedBooks as $book)
            @include('partials.book-card', ['book' => $book, 'height' => 'short'])
        @endforeach
    </div>

    <h2 class="text-2xl font-bold mb-4">Browse Books</h2>
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4">
        @foreach($this->getBooks() as $book)
            @include('partials.book-card', ['book' => $book, 'height' => 'short'])
        @endforeach
    </div>

    <div class="mt-4">
        {{ $this->getBooks()->links() }}
    </div>
</x-filament-panels::page>
