@props(['label', 'locations' => [], 'small' => false])

@isset($label)
    <span class="block break-words text-sm font-medium text-gray-700 dark:text-gray-400">
        {{ $label }}
    </span>
    @endif
    {{-- relative border border-slate-300 shadow-lg --}}
    <div x-data="map()" class="{{ $attributes->get('class') }}" x-init="initComponent(@js($locations), @js($small));">
        <div x-ref="map" wire:ignore class="ol-map h-full w-full overflow-hidden rounded-md border border-slate-300"></div>
    </div>
