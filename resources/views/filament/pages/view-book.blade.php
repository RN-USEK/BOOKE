<x-filament-panels::page>
  @if($this->record)
    <div class="flex flex-col lg:flex-row lg:space-x-4">
      <div class="flex-1 max-w-2xl mx-auto"> <!-- Main content section -->
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
              <button wire:click="toggleWishlist({{ $this->record->id }})" class="{{ $this->isInViewBookWishlist() ? 'text-danger-600' : 'text-gray-400' }} hover:text-danger-900 dark:hover:text-danger-400">
                <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd"/>
                </svg>
              </button>
              <button class="text-success-600 hover:text-success-900 dark:text-success-500 dark:hover:text-success-400" onclick="toggleCartVisibility(event)">
                <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 20 20">
                  <path d="M3 1a1 1 0 000 2h1.22l.305 1.222a.997.997 0 00.01.042l1.358 5.43-.893.892C3.74 11.846 4.632 14 6.414 14H15a1 1 0 000-2H6.414l1-1H14a1 1 0 00.894-.553l3-6A1 1 0 0017 3H6.28l-.31-1.243A1 1 0 005 1H3zM16 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM6.5 18a1.5 1.5 0 100-3 1.5 1.5 0 000 3z"/>
                </svg>
              </button>
            </div>
          </div>
        </div>
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

      <!-- Floating Cart Icon -->
      <div class="fixed bottom-4 right-4 z-50">
        <button onclick="toggleCartVisibility(event)" class="bg-white dark:bg-gray-800 shadow rounded-full p-3 focus:outline-none">
          <svg id="cart-icon" class="h-6 w-6 text-gray-600 dark:text-gray-400" fill="currentColor" viewBox="0 0 20 20">
            <path d="M3 1a1 1 0 000 2h1.22l.305 1.222a.997.997 0 00.01.042l1.358 5.43-.893.892C3.74 11.846 4.632 14 6.414 14H15a1 1 0 000-2H6.414l1-1H14a1 1 0 00.894-.553l3-6A1 1 0 0017 3H6.28l-.31-1.243A1 1 0 005 1H3zM16 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM6.5 18a1.5 1.5 0 100-3 1.5 1.5 0 000 3z"/>
          </svg>
        </button>
      </div>
    </div>
  @else
    <div class="text-center text-gray-500 dark:text-gray-400">
      No book data available.
    </div>
  @endif

 <!-- Reviews section -->
    <div class="mt-8 max-w-4xl mx-auto">
    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg p-6">
        <h2 class="text-2xl font-bold mb-4">Reviews</h2>
        
        @if($this->record && $this->record->reviews->count() > 0)
        <div class="flex items-center mb-6">
            <span class="text-lg font-semibold mr-2">Average Rating:</span>
            <span class="text-lg font-semibold mr-2">{{ number_format($this->record->averageRating(), 1) }}</span>
            <span class="flex">
            @php
                $averageRating = round($this->record->averageRating());
            @endphp
            @for ($i = 1; $i <= 5; $i++)
                <svg class="h-6 w-6" style="{{ $i <= $averageRating ? 'color: #facc15;' : 'color: #d1d5db;' }}" fill="currentColor" viewBox="0 0 20 20">
                <path d="M10 15l-5.392 2.838 1.03-6.01L1 6.75l6.02-.876L10 1l2.98 4.874 6.02.876-4.638 4.078 1.03 6.01L10 15z"/>
                </svg>
            @endfor
            </span>
        </div>

        @foreach($this->record->reviews as $review)
            <div class="bg-gray-100 dark:bg-gray-900 overflow-hidden shadow rounded-lg p-4 mb-4">
            <div class="flex items-center mb-2">
                <span class="font-semibold mr-2">Rating:</span>
                <span class="flex">
                @for ($i = 1; $i <= 5; $i++)
                    <svg class="h-5 w-5" style="{{ $i <= $review->rating ? 'color: #facc15;' : 'color: #d1d5db;' }}" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M10 15l-5.392 2.838 1.03-6.01L1 6.75l6.02-.876L10 1l2.98 4.874 6.02.876-4.638 4.078 1.03 6.01L10 15z"/>
                    </svg>
                @endfor
                </span>
            </div>
            <p class="text-gray-700 dark:text-gray-300 mb-2">{{ $review->comment }}</p>
            <p class="text-sm text-gray-500">By {{ $review->user->name }} on {{ $review->created_at->format('M d, Y') }}</p>
            </div>
        @endforeach
        @else
        <p class="text-gray-500">No reviews yet.</p>
        @endif

        @if($this->record && $this->isBookPurchased())
            @if($this->hasUserReviewed())
                @php
                    $this->userReview = $this->getUserReview();
                    $this->rating = $this->userReview->rating;
                    $this->comment = $this->userReview->comment;
                @endphp
                <div class="mt-6">
                    <form wire:submit.prevent="updateReview" class="space-y-4">
                        <div class="flex items-center">
                            <span class="font-semibold mr-2">Rating:</span>
                            <div class="flex">
                                @for ($i = 1; $i <= 5; $i++)
                                    <button type="button" wire:click="setRating({{ $i }})" class="{{ $this->rating >= $i ? 'text-yellow-500' : 'text-gray-400' }} hover:text-yellow-500 focus:outline-none">
                                        <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M10 15l-5.392 2.838 1.03-6.01L1 6.75l6.02-.876L10 1l2.98 4.874 6.02.876-4.638 4.078 1.03 6.01L10 15z"/>
                                        </svg>
                                    </button>
                                @endfor
                            </div>
                        </div>
                        <div>
                            <textarea wire:model="comment" class="w-full h-24 bg-gray-100 dark:bg-gray-700 rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Write your review..."></textarea>
                        </div>
                        <div class="flex justify-end">
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Update Review
                            </button>
                        </div>
                    </form>
                </div>
            @else
                <div class="mt-6">
                    <form wire:submit.prevent="submitReview" class="space-y-4">
                        <div class="flex items-center">
                            <span class="font-semibold mr-2">Rating:</span>
                            <div class="flex">
                              @for ($i = 1; $i <= 5; $i++)
                                          <button type="button" wire:click="setRating({{ $i }})" class="{{ $this->rating >= $i ? 'text-yellow-500' : 'text-gray-400' }} hover:text-yellow-500 focus:outline-none">
                                              <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 20 20">
                                                  <path d="M10 15l-5.392 2.838 1.03-6.01L1 6.75l6.02-.876L10 1l2.98 4.874 6.02.876-4.638 4.078 1.03 6.01L10 15z"/>
                                              </svg>
                                          </button>
                                      @endfor
                                  </div>
                              </div>
                              <div>
                                  <textarea wire:model="comment" class="w-full h-24 bg-gray-100 dark:bg-gray-700 rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Write your review..."></textarea>
                              </div>
                              <div class="flex justify-end">
                                  <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                      Submit Review
                                  </button>
                              </div>
                          </form>
                      </div>
                  @endif
                  @elseif($this->record)
                      <p class="text-gray-500">You need to purchase this book to leave a review.</p>
                  @endif
          </div>
      </div>
      <div class="mt-4 max-w-2xl mx-auto"> <!-- Center the footer action as well -->
    {{ $this->getFooterActions()[0]->render() }}
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

