<x-app-layout>
    <div class="mx-auto max-w-7xl pt-6 sm:px-6 lg:px-8">
        <x-slot name="title">
            {{ __('tag.my-tags') }}
        </x-slot>
        <x-card color="my-2 backdrop-blur-md bg-white/50" rounded="rounded-none sm:rounded-lg">
            <h3 class="break-words pb-2 text-2xl font-semibold text-gray-800 ml-4">My tags</h3>
            <livewire:tag.create-tags/>
        </x-card>
        <x-card color="mt-8 backdrop-blur-md bg-indigo-50/50" rounded="rounded-none sm:rounded-lg">
            <h3 class="break-words pb-2 text-2xl font-semibold text-gray-800 ml-4">Follow tags</h3>
            <livewire:tag.follow-tags/>
        </x-card>
    </div>
</x-app-layout>
