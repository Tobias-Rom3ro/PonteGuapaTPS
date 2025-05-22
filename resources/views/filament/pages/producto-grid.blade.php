<x-filament::page>
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4">
        @foreach($records as $record)
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="aspect-w-1 aspect-h-1">
                    <img src="{{ $record->getImagenUrl() }}"
                         alt="{{ $record->nombre }}"
                         class="w-full h-full object-cover">
                </div>
                <div class="p-4">
                    <h3 class="text-lg font-medium text-gray-900">{{ $record->nombre }}</h3>
                    <p class="mt-1 text-sm text-gray-500">{{ Str::limit($record->descripcion, 100) }}</p>
                    <div class="mt-2 flex items-center justify-between">
                        <span class="text-lg font-bold text-primary-600">{{ number_format($record->precio, 2) }}€</span>
                        <span class="text-sm text-gray-500">Stock: {{ $record->stock }}</span>
                    </div>
                    <div class="mt-4 flex justify-end space-x-2">
                        @if(auth()->user()->can('manage productos'))
                            <x-filament::button
                                size="sm"
                                icon="heroicon-o-pencil"
                                :href="ProductoResource::getUrl('edit', ['record' => $record])"
                            >
                                Editar
                            </x-filament::button>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="mt-4">
        {{ $records->links() }}
    </div>
</x-filament::page>
