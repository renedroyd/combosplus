@php
    $hasSteps = count($steps) > 0;
@endphp

<x-filament-widgets::widget>
    <x-filament::section
        heading="Pon tu tienda en marcha"
        description="Completa estos pasos para preparar tu catálogo en el Marketplace."
    >
        @if ($hasSteps)
            <div class="space-y-4">
                <div class="flex items-center justify-between gap-4">
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-200">{{ $progress }}% completado</span>
                    <div class="h-2 flex-1 overflow-hidden rounded-full bg-gray-200 dark:bg-gray-700">
                        <div class="h-full rounded-full bg-primary-600" style="width: {{ $progress }}%"></div>
                    </div>
                </div>

                <div class="grid gap-3 md:grid-cols-2">
                    @foreach ($steps as $step)
                        <a href="{{ $step['url'] }}"
                           class="flex items-start gap-3 rounded-xl border p-4 transition hover:bg-gray-50 dark:hover:bg-gray-800 {{ $step['done'] ? 'border-primary-200 dark:border-primary-800' : 'border-gray-200 dark:border-gray-700' }}">
                            <span class="mt-0.5 flex h-6 w-6 shrink-0 items-center justify-center rounded-full text-xs font-bold {{ $step['done'] ? 'bg-primary-600 text-white' : 'border border-gray-300 text-gray-500 dark:border-gray-600' }}">
                                {{ $step['done'] ? '✓' : $loop->iteration }}
                            </span>
                            <span>
                                <span class="block font-medium text-gray-950 dark:text-white">{{ $step['title'] }}</span>
                                <span class="mt-1 block text-sm text-gray-500 dark:text-gray-400">{{ $step['description'] }}</span>
                            </span>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif
    </x-filament::section>
</x-filament-widgets::widget>
