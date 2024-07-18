<x-filament-panels::page>
    <form wire:submit.prevent="submit">
        {{ $this->form }}

        <div class="mt-4">
            <h2 class="text-lg font-semibold mb-2">Order Summary</h2>
            @foreach($this->getCartContent() as $bookId => $item)
                <div class="flex justify-between items-center mb-4">
                    <div class="flex items-center">
                        @if(isset($item['cover_image']))
                            <img src="{{ $item['cover_image'] }}" alt="{{ $item['title'] }}" 
                                 style="width: 48px; height: 48px; object-fit: cover; border-radius: 4px; margin-right: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                        @else
                            <div style="width: 48px; height: 48px; background-color: #f3f4f6; border-radius: 4px; margin-right: 8px; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                                <span style="font-size: 10px; color: #9ca3af;">No Image</span>
                            </div>
                        @endif
                        <span>{{ $item['title'] }} (x{{ $item['quantity'] }})</span>
                    </div>
                    <span>${{ number_format($item['price'] * $item['quantity'], 2) }}</span>
                </div>
            @endforeach
            <div class="border-t pt-2 mt-2">
                <div class="flex justify-between items-center font-semibold">
                    <span>Total:</span>
                    <span>${{ number_format($this->getCartTotal(), 2) }}</span>
                </div>
            </div>
        </div>

        <x-filament::button type="submit" class="mt-4">
            Place Order
        </x-filament::button>
    </form>
</x-filament-panels::page>