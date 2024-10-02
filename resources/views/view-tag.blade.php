<x-app-layout>
    <x-slot name="title">
        {{ __('tag.view') }} {{ $uid }}
    </x-slot>
    <div class="pt-6">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <x-card rounded="rounded-none sm:rounded-lg">
                <livewire:tag.view :uid="$uid" />
            </x-card>
        </div>
    </div>
</x-app-layout>
