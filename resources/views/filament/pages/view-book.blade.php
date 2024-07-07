<x-filament-panels::page>
    @if($this->record)
        <div class="max-w-2xl mx-auto"> <!-- This div centers and constrains the width -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
                <div class="p-6">
                    <div class="flex flex-col items-center mb-6">
                        @if($this->record->cover_image)
                            <img src="{{ $this->record->cover_image }}" alt="{{ $this->record->title }}" class="w-full h-auto object-cover rounded mb-4">
                        @else
                            <div class="w-full h-64 bg-gray-200 dark:bg-gray-700 flex items-center justify-center rounded mb-4">
                                <span class="text-gray-500 dark:text-gray-400">No Image</span>
                            </div>
                        @endif
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white text-center">{{ $this->record->title }}</h2>
                    </div>
                    
                    <div class="flex flex-col">
                        <p class="text-gray-600 dark:text-gray-300 mb-2 text-center">By {{ $this->record->author }}</p>
                        <p class="text-lg font-bold text-gray-900 dark:text-white mb-4 text-center">${{ number_format($this->record->price, 2) }}</p>
                        <p class="text-black dark:text-white mb-4">{{ $this->record->description }}</p>
                        <p class="text-gray-600 dark:text-gray-300">ISBN: {{ $this->record->isbn }}</p>
                        <p class="text-gray-600 dark:text-gray-300">In Stock: {{ $this->record->stock }}</p>
                        
                        @foreach($this->record->getAttributes() as $key => $value)
                            @if(!in_array($key, ['id', 'title', 'author', 'description', 'isbn', 'price', 'stock', 'cover_image', 'created_at', 'updated_at', 'category_id']))
                                <p class="text-gray-600 dark:text-gray-300">
                                    {{ ucfirst($key) }}: 
                                    @if(is_array($value))
                                        {{ json_encode($value) }}
                                    @elseif(is_object($value))
                                        {{ method_exists($value, '__toString') ? $value : json_encode($value) }}
                                    @else
                                        {{ $value }}
                                    @endif
                                </p>
                            @endif
                        @endforeach
                    </div>
                    <div class="flex space-x-2">
                        <button wire:click="toggleWishlist" class="{{ $this->isInWishlist() ? 'text-danger-600' : 'text-gray-400' }} hover:text-danger-900 dark:hover:text-danger-400">
                            <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd"/>
                            </svg>
                        </button>
                        <button class="text-success-600 hover:text-success-900 dark:text-success-500 dark:hover:text-success-400">
                            <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M3 1a1 1 0 000 2h1.22l.305 1.222a.997.997 0 00.01.042l1.358 5.43-.893.892C3.74 11.846 4.632 14 6.414 14H15a1 1 0 000-2H6.414l1-1H14a1 1 0 00.894-.553l3-6A1 1 0 0017 3H6.28l-.31-1.243A1 1 0 005 1H3zM16 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM6.5 18a1.5 1.5 0 100-3 1.5 1.5 0 000 3z"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="text-center text-gray-500 dark:text-gray-400">
            No book data available.
        </div>
    @endif
    
    <div class="mt-4 max-w-2xl mx-auto"> <!-- Center the footer action as well -->
        {{ $this->getFooterActions()[0]->render() }}
    </div>
    <!-- Cart Section -->
    <div class="mt-8 max-w-2xl mx-auto">
        <h2 class="text-2xl font-bold mb-4">Your Cart</h2>
        @php
            $cartContent = $this->getCartContent();
            $cartTotal = $this->getCartTotal();
        @endphp

        @if(count($cartContent) > 0)
            @foreach($cartContent as $bookId => $item)
                <div class="flex items-center justify-between border-b py-2">
                    <div>
                        <h3 class="font-semibold">{{ $item['title'] }}</h3>
                        <p>Price: ${{ number_format($item['price'], 2) }}</p>
                        <div class="flex items-center">
                            <button wire:click="updateCartQuantity({{ $bookId }}, {{ $item['quantity'] - 1 }})" class="px-2 py-1 bg-gray-200 rounded">-</button>
                            <span class="mx-2">{{ $item['quantity'] }}</span>
                            <button wire:click="updateCartQuantity({{ $bookId }}, {{ $item['quantity'] + 1 }})" class="px-2 py-1 bg-gray-200 rounded">+</button>
                        </div>
                    </div>
                    <button wire:click="removeFromCart({{ $bookId }})" class="text-red-500">Remove</button>
                </div>
            @endforeach
            <div class="mt-4">
                <p class="font-bold">Total: ${{ number_format($cartTotal, 2) }}</p>
                <button class="mt-2 px-4 py-2 bg-blue-500 text-white rounded">Proceed to Checkout</button>
            </div>
        @else
            <p>Your cart is empty.</p>
        @endif
    </div>
</x-filament-panels::page>