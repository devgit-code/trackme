<x-app-layout>
    <div class="mx-auto max-w-2xl pt-6 sm:p-6 lg:p-8">
        <x-slot name="title">
            {{ __('tag.wrong') }}
        </x-slot>
        <x-card title="{{ __('tag.wrong') }}" rounded="rounded-none sm:rounded-lg">
            <livewire:tag.wrong/>
        </x-card>
    </div>
</x-app-layout>
