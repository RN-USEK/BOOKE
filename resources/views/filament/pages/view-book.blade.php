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
</x-filament-panels::page>