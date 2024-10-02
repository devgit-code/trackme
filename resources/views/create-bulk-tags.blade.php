<x-app-layout>
    <div class="mx-auto max-w-2xl pt-6 sm:p-6 lg:p-8">
        <x-slot name="title">
            {{ __('tag.create-bulk') }}
        </x-slot>
        <x-card title="{{ __('tag.create-bulk') }}" rounded="rounded-none sm:rounded-lg">
            <livewire:tag.create-bulk />
        </x-card>
    </div>
</x-app-layout>
