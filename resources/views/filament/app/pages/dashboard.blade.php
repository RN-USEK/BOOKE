<x-filament-panels::page>
    <form wire:submit.prevent="search" class="mb-6">
        {{ $this->form }}
        <x-filament::button type="submit" class="mt-4">
            Search
        </x-filament::button>
    </form>

    @if($this->searchResults !== null)
        <h2 class="text-2xl font-bold mb-4">Your Results</h2>
    @endif

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4">
        @foreach($this->getBooks() as $book)
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
                <div class="p-4">
                    @if($book->cover_image)
                        <img src="{{ $book->cover_image }}" alt="{{ $book->title }}" class="w-full h-48 object-cover mb-4">
                    @else
                        <div class="w-full h-48 bg-gray-200 dark:bg-gray-700 flex items-center justify-center mb-4">
                            <span class="text-gray-500 dark:text-gray-400">No Image</span>
                        </div>
                    @endif
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white truncate">{{ $book->title }}</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-300">{{ $book->author }}</p>
                    <p class="text-lg font-bold text-gray-900 dark:text-white mt-2">${{ number_format($book->price, 2) }}</p>
                </div>
                <div class="px-4 py-3 bg-gray-50 dark:bg-gray-700 flex justify-between">
                    <a href="{{ route('filament.app.pages.view-book') }}?recordId={{ $book->id }}" class="text-primary-600 hover:text-primary-900 dark:text-primary-500 dark:hover:text-primary-400">
                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/>
                            <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/>
                        </svg>
                    </a>
                    <button class="text-danger-600 hover:text-danger-900 dark:text-danger-500 dark:hover:text-danger-400">
                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd"/>
                        </svg>
                    </button>
                    <button class="text-success-600 hover:text-success-900 dark:text-success-500 dark:hover:text-success-400">
                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M3 1a1 1 0 000 2h1.22l.305 1.222a.997.997 0 00.01.042l1.358 5.43-.893.892C3.74 11.846 4.632 14 6.414 14H15a1 1 0 000-2H6.414l1-1H14a1 1 0 00.894-.553l3-6A1 1 0 0017 3H6.28l-.31-1.243A1 1 0 005 1H3zM16 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM6.5 18a1.5 1.5 0 100-3 1.5 1.5 0 000 3z"/>
                        </svg>
                    </button>
                </div>
            </div>
        @endforeach
    </div>
</x-filament-panels::page>