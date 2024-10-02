<x-app-layout>
    <div class="mx-auto max-w-3xl pt-6 sm:px-6 lg:px-8">
        <x-slot name="title">
            {{ __('tag.print') }} {{ $uid }}
        </x-slot>
        <x-card title="{{ __('tag.print') }}" rounded="rounded-none sm:rounded-lg">
            <livewire:tag.print :uid="$uid" />
        </x-card>
    </div>
</x-app-layout>
