<x-filament-panels::page>
    <form wire:submit.prevent="submit">
        {{ $this->form }}

        <div class="mt-4">
            <h2 class="text-lg font-semibold mb-2">Order Summary</h2>
            @foreach($this->getCartContent() as $bookId => $item)
                <div class="flex justify-between items-center mb-2">
                    <span>{{ $item['title'] }} (x{{ $item['quantity'] }})</span>
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