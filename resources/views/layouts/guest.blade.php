<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name') }} - {{ $title ?? 'Page' }}</title>

    <link rel="icon" type="image/svg" href="/favicon.svg">

    <!-- Scripts -->
    @wireUiScripts()
    @livewireStyles()
    @livewireScriptConfig
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer data-domain="trackme.info" src="https://plausible.isaacs.site/js/script.js"></script>
</head>

<body class="font-sans antialiased">
    <img class="custom-bg-fix" src="{{ Vite::asset('resources/images/locks-on-fence.webp') }}" alt="">
    <div class="w-full">
        <div class="flex justify-center py-6">
            <a href="/" wire:navigate>
                <x-application-logo text="true" shadow
                    class="block h-12 w-auto fill-current text-neutral-50 mix-blend-difference" />
            </a>
        </div>

        <div class="mx-auto w-full sm:max-w-md">
            <x-card rounded="rounded-none sm:rounded-lg">
                {{ $slot }}
            </x-card>
        </div>

        <div class="mt-8 flex justify-center px-0">
            <div class="text-center text-neutral-50">
                <div class="flex items-center gap-4">
                    <a href="https://gitlab.com/tenten8401" class="group inline-flex items-center">
                        Made with <x-icon name="heart" class="mx-1 h-5 w-5" /> by Isaac A.
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
