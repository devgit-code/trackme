<x-app-layout>
    <div class="mx-auto max-w-7xl pt-6 sm:px-6 lg:px-8">
        <x-slot name="title">
            {{ __('tag.my-tags') }}
        </x-slot>
        <x-card color="backdrop-blur-md bg-white/50" rounded="rounded-none sm:rounded-lg">
            <livewire:tag.my-tags />
        </x-card>
    </div>
</x-app-layout>
