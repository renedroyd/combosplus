<x-filament-panels::page>
    @php
        $data = $this->getViewData();
        $status = $data['status'];
        $published = $status->value === 'published';
    @endphp

    <div class="space-y-6">
        <x-filament::section>
            <x-slot name="heading">Estado del catálogo</x-slot>
            <x-slot name="description">Controla cuándo tu tienda está visible en el Marketplace.</x-slot>

            <div class="flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <div class="text-lg font-semibold text-gray-950 dark:text-white">{{ $status->label() }}</div>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        @if ($published)
                            Tu catálogo está disponible para clientes.
                        @elseif ($status->value === 'unpublished')
                            Tu catálogo está preparado, pero no aparece en el Marketplace.
                        @else
                            Tu catálogo todavía no ha sido publicado.
                        @endif
                    </p>
                </div>

                <div class="flex flex-wrap gap-2">
                    @if (! $published)
                        <x-filament::button wire:click="publish" icon="heroicon-m-globe-alt">
                            Publicar catálogo
                        </x-filament::button>
                    @else
                        <x-filament::button color="gray" wire:click="unpublish" icon="heroicon-m-eye-slash">
                            No publicar
                        </x-filament::button>
                    @endif
                </div>
            </div>
        </x-filament::section>

        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <x-filament::section>
                <div class="text-sm text-gray-500 dark:text-gray-400">Perfil</div>
                <div class="mt-1 font-semibold">{{ $data['profileComplete'] ? 'Completo' : 'Pendiente' }}</div>
            </x-filament::section>
            <x-filament::section>
                <div class="text-sm text-gray-500 dark:text-gray-400">Categorías</div>
                <div class="mt-1 font-semibold">{{ $data['categoryCount'] }}</div>
            </x-filament::section>
            <x-filament::section>
                <div class="text-sm text-gray-500 dark:text-gray-400">Productos</div>
                <div class="mt-1 font-semibold">{{ $data['productCount'] }}</div>
            </x-filament::section>
            <x-filament::section>
                <div class="text-sm text-gray-500 dark:text-gray-400">Productos visibles</div>
                <div class="mt-1 font-semibold">{{ $data['visibleProductCount'] }}</div>
            </x-filament::section>
        </div>

        @if (! $published)
            <x-filament::section>
                <x-slot name="heading">Antes de publicar</x-slot>
                <ul class="space-y-2 text-sm text-gray-600 dark:text-gray-300">
                    <li>• Completa el nombre y la URL pública de la tienda.</li>
                    <li>• Crea al menos una categoría.</li>
                    <li>• Añade al menos un producto.</li>
                    <li>• Asegúrate de que al menos un producto sea visible.</li>
                </ul>
            </x-filament::section>
        @endif
    </div>
</x-filament-panels::page>
