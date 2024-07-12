<x-filament-panels::page class="bg-gray-100 dark:bg-gray-900">
    <!-- Hero Section -->
    <div class="relative mb-8">
        <img src="{{ asset('hero-books.png') }}" alt="Hero Image" class="w-full h-64 object-cover">
        <div class="absolute inset-0 bg-black bg-opacity-50 flex items-center justify-center">
            <h1 class="text-4xl font-bold text-white">Welcome to Our Bookstore</h1>
        </div>
    </div>

 

    <!-- Popular Books Section -->
    <h2 class="text-2xl font-bold mb-4">Popular Books</h2>
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4 mb-8">
        @foreach($this->popularBooks as $book)
            @include('partials.book-card', ['book' => $book])
        @endforeach
    </div>

    <!-- Browse By Category Section -->
    <h2 class="text-2xl font-bold mb-4">Browse By Category</h2>
    <div class="flex flex-wrap gap-4 mb-8">
    @php
        $colors = ['red', 'blue', 'green', 'yellow', 'purple', 'pink', 'indigo', 'teal', 'orange', 'cyan', 'lime','violet', 'fuchsia', 'gray', 'brown'];
        $colorIndex = 0;
    @endphp

    @foreach($this->categories as $category)
        @php
            $color = $colors[$colorIndex % count($colors)];
            $colorIndex++;
        @endphp
        <a href="{{ route('category-books', ['category' => $category->id]) }}"
        class="px-4 py-2 rounded-full text-white font-semibold" 
            style="background-color: {{ $color }}" onclick="console.log('URL:', `{{ route('category-books', ['category' => $category->id]) }}`)">
            {{ $category->name }}
        </a>
    @endforeach

    </div>
   <!-- Search Form -->
   <form wire:submit.prevent="search" class="mb-8 bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
        {{ $this->form }}
        <x-filament::button type="submit" class="mt-4">
            Search
        </x-filament::button>
    </form>
    @if($this->searchResults !== null)
        <h2 class="text-2xl font-bold mb-4">Your Results</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4 mb-8">
            @foreach($this->searchResults as $book)
                @include('partials.book-card', ['book' => $book])
            @endforeach
        </div>
    @endif

    <h2 class="text-2xl font-bold mb-4">For You</h2>
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4 mb-8">
        @foreach($this->recommendedBooks as $book)
            @include('partials.book-card', ['book' => $book])
        @endforeach
    </div>

    <h2 class="text-2xl font-bold mb-4">Browse Books</h2>
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
        @foreach($this->getBooks() as $book)
            @include('partials.book-card', ['book' => $book])
        @endforeach
    </div>

    <div class="mt-4">
        {{ $this->getBooks()->links() }}
    </div>
</x-filament-panels::page>