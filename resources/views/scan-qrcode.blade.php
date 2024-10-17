<x-app-layout>
    <div class="mx-auto max-w-3xl p-0 sm:px-6 sm:pt-6 lg:px-8">
        <x-slot name="title">
            {{ __('tag.scan') }}
        </x-slot>
        <x-card padding="m-auto p-0 sm:px-2 sm:py-5 md:px-4" rounded="rounded-none sm:rounded-lg">
            {{-- <p class="text-center py-2">Camera Initializing</p> --}}
            <video class="qr-scanner sm:rounded sm:shadow">
            </video>
            {{--<p class="py-2 text-center">Scanning is done locally in your browser, nothing is sent to the server. The
                <x-button 2xs outline href="https://github.com/nimiq/qr-scanner" label="qr-scanner" /> JS library is used
                for this.</p> --}}
        </x-card>
    </div>
</x-app-layout>
