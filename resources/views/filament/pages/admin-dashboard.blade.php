<x-filament::page>
    <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
        @foreach ($this->getHeaderWidgets() as $widget)
            @if ($widget instanceof \Filament\Widgets\Widget)
                {{ $widget }}
            @endif
        @endforeach
    </div>

    <div class="mt-8 grid grid-cols-1 gap-4 md:grid-cols-2">
        @foreach ($this->getFooterWidgets() as $widget)
            @if ($widget instanceof \Filament\Widgets\Widget)
                {{ $widget }}
            @endif
        @endforeach
    </div>
</x-filament::page>