<x-filament-panels::page>
    <div class="flex flex-col items-center space-y-6">
        <div class="w-full max-w-5xl">
            <x-filament-tables::table>
                {{ $this->table }}
            </x-filament-tables::table>
        </div>
    </div>
</x-filament-panels::page> 