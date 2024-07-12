<x-filament-panels::page>
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
        /* .fi-page {
            background-color: white;
        } */
        /* .fi-sidebar {
            background-color: peachpuff;
        } */
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

    <!-- Hero Section -->
    <div class="relative mb-8">
        <img src="{{ asset('hero-books.png') }}" alt="Hero Image" class="w-full h-64 object-cover">
        <div class="absolute inset-0 bg-black bg-opacity-50 flex items-center justify-center">
            <h1 class="text-4xl font-bold text-white">Welcome to Our Bookstore</h1>
        </div>
    </div>

    <div class="container">
        <!-- Popular Books Section -->
        <h2 class="text-2xl font-bold mb-4">Popular Books</h2>
        <div class="book-grid mb-8">
            @foreach($this->popularBooks as $book)
                @include('partials.book-card', ['book' => $book])
            @endforeach
        </div>

        <!-- Browse By Category Section -->
        <h2 class="text-2xl font-bold mb-4">Browse By Category</h2>
        <div class="flex flex-wrap gap-4 mb-8">
            @php
                $colors = ['red', 'blue', 'green', 'yellow', 'purple', 'pink', 'indigo', 'teal', 'orange', 'cyan', 'lime', 'violet', 'fuchsia', 'gray', 'brown'];
                $colorIndex = 0;
            @endphp

            @foreach($this->categories as $category)
                @php
                    $color = $colors[$colorIndex % count($colors)];
                    $colorIndex++;
                @endphp
                <a href="{{ route('category-books', ['category' => $category->id]) }}"
                class="px-4 py-2 rounded-full text-white font-semibold" 
                    style="background-color: {{ $color }}">
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
            <div class="book-grid mb-8">
                @foreach($this->searchResults as $book)
                    @include('partials.book-card', ['book' => $book])
                @endforeach
            </div>
        @endif

        <h2 class="text-2xl font-bold mb-4">For You</h2>
        <div class="book-grid mb-8">
            @foreach($this->recommendedBooks as $book)
                @include('partials.book-card', ['book' => $book])
            @endforeach
        </div>

        <h2 class="text-2xl font-bold mb-4">Browse Books</h2>
        <div class="book-grid mb-8">
            @foreach($this->getBooks() as $book)
                @include('partials.book-card', ['book' => $book])
            @endforeach
        </div>

        <div class="mt-4">
            {{ $this->getBooks()->links() }}
        </div>
    </div>
    
    <!-- Floating Cart Icon -->
<div class="fixed bottom-4 right-4 z-50">
    <button onclick="toggleCartVisibility(event)" class="bg-white dark:bg-gray-800 shadow rounded-full p-3 focus:outline-none">
        <svg id="cart-icon" class="h-6 w-6 text-gray-600 dark:text-gray-400" fill="currentColor" viewBox="0 0 20 20">
            <path d="M3 1a1 1 0 000 2h1.22l.305 1.222a.997.997 0 00.01.042l1.358 5.43-.893.892C3.74 11.846 4.632 14 6.414 14H15a1 1 0 000-2H6.414l1-1H14a1 1 0 00.894-.553l3-6A1 1 0 0017 3H6.28l-.31-1.243A1 1 0 005 1H3zM16 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM6.5 18a1.5 1.5 0 100-3 1.5 1.5 0 000 3z"/>
        </svg>
    </button>
</div>

<!-- Cart Section -->
<div id="cart-section" class="w-full lg:w-64 fixed top-16 right-4 lg:top-0 lg:right-0 lg:static lg:max-w-xs lg:ml-auto hidden">
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 relative">
        <button onclick="toggleCartVisibility(event)" class="absolute top-4 right-4 text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200">
            <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
            </svg>
        </button>
        <h2 class="text-2xl font-bold mb-4 text-center">Your Cart</h2>
        @php
            $cartContent = $this->getCartContent();
            $cartTotal = $this->getCartTotal();
        @endphp

        @if($cartContent && count($cartContent) > 0)
            @foreach($cartContent as $bookId => $item)
                <div class="flex items-center justify-between border-b py-2">
                    <div>
                        <h3 class="font-semibold">{{ $item['title'] }}</h3>
                        <p>Price: ${{ number_format($item['price'], 2) }}</p>
                        <div class="flex items-center">
                            <!-- <button wire:click="updateCartQuantity({{ $bookId }}, {{ $item['quantity'] - 1 }})" class="px-2 py-1 bg-gray-200 rounded" onclick="event.stopPropagation();">-</button> -->
                            <!-- <span class="mx-2">{{ $item['quantity'] }}</span> -->
                            <!-- <button wire:click="updateCartQuantity({{ $bookId }}, {{ $item['quantity'] + 1 }})" class="px-2 py-1 bg-gray-200 rounded" onclick="event.stopPropagation();">+</button> -->
                        </div>
                    </div>
                    <button wire:click="removeFromCart({{ $bookId }})" class="text-red-500" onclick="event.stopPropagation();">Remove</button>
                </div>
            @endforeach
            <div class="mt-4">
                <p class="font-bold">Total: ${{ number_format($cartTotal, 2) }}</p>
                <button wire:click="proceedToCheckout" style="margin-top: 0.5rem; padding-left: 1rem; padding-right: 1rem; padding-top: 0.5rem; padding-bottom: 0.5rem; background-color: #3b82f6; color: white; border-radius: 0.25rem; transition: background-color 0.2s ease-in-out;" onmouseover="this.style.backgroundColor='#1e40af';" onmouseout="this.style.backgroundColor='#3b82f6';">Proceed to Checkout</button>
            </div>
        @else
            <p>Your cart is empty.</p>
        @endif
    </div>
</div>

<script>
    function toggleCartVisibility(event) {
        event.stopPropagation();
        var cartSection = document.getElementById('cart-section');
        var cartIcon = document.getElementById('cart-icon');
        if (cartSection.classList.contains('hidden')) {
            cartSection.classList.remove('hidden');
            cartIcon.innerHTML = `<svg class="h-4 w-4 text-gray-600 dark:text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
            </svg>`;
        } else {
            cartSection.classList.add('hidden');
            cartIcon.innerHTML = `<svg class="h-6 w-6 text-gray-600 dark:text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                <path d="M3 1a1 1 0 000 2h1.22l.305 1.222a.997.997 0 00.01.042l1.358 5.43-.893.892C3.74 11.846 4.632 14 6.414 14H15a1 1 0 000-2H6.414l1-1H14a1 1 0 00.894-.553l3-6A1 1 0 0017 3H6.28l-.31-1.243A1 1 0 005 1H3zM16 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM6.5 18a1.5 1.5 0 100-3 1.5 1.5 0 000 3z"/>
            </svg>`;
        }
    }

    document.querySelectorAll('#cart-section button').forEach(button => {
        button.addEventListener('click', function(event) {
            event.stopPropagation();
        });
    });
</script>
</x-filament-panels::page>