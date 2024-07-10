<x-filament-panels::page>
    <form wire:submit.prevent="search" class="mb-6">
        {{ $this->form }}
        <x-filament::button type="submit" class="mt-4">
            Search
        </x-filament::button>
    </form>

    @if($this->searchResults !== null)
        <h2 class="text-2xl font-bold mb-4">Your Results</h2>
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 mb-8">
            @foreach($this->searchResults as $book)
                @include('partials.book-card', ['book' => $book])
            @endforeach
        </div>
    @endif

    <h2 class="text-2xl font-bold mb-4">For You</h2>
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 mb-8">
        @foreach($this->recommendedBooks as $book)
            @include('partials.book-card', ['book' => $book])
        @endforeach
    </div>

    <h2 class="text-2xl font-bold mb-4">Browse Books</h2>
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4">
        @foreach($this->getBooks() as $book)
            @include('partials.book-card', ['book' => $book])
        @endforeach
    </div>

    <div class="mt-4">
        {{ $this->getBooks()->links() }}
    </div>
</x-filament-panels::page>