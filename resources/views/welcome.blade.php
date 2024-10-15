<x-app-layout>
    <div class="mx-auto max-w-7xl pt-6 sm:px-6 lg:px-8">
        <x-slot name="title">
            {{ __('tag.my-tags') }}
        </x-slot>
        <div class="pt-6">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <x-card rounded="rounded-none sm:rounded-lg">
                    <livewire:layout.home />
                </x-card>
            </div>
        </div>
    </div>
</x-app-layout>
