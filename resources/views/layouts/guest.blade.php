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
    <!-- <script defer data-domain="trackme.info" src="https://plausible.isaacs.site/js/script.js"></script> -->

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&family=Manrope:wght@200..800&display=swap" rel="stylesheet">
</head>

<body class="font-manrope antialiased  bg-gradient-to-r from-gray-300 via-blue-200 to-gray-300">
    <!-- <img class="custom-bg-fix" src="{{ Vite::asset('resources/images/screen.png') }}" alt=""> -->

    <div class="grid sm:grid-cols-2 gap-4">
        <div class="hidden sm:block">
            <img class="min-h-screen object-cover" src="{{ Vite::asset('resources/images/screen.png') }}" alt="">
        </div>
        <div>
            <div class="flex justify-center pt-10 ppb-3">
                <a href="/" wire:navigate>
                    <x-application-logo text="true" shadow
                        class="block h-12 w-auto fill-current text-neutral-50 mix-blend-difference" />
                </a>
            </div>

            <div class="mx-auto w-full sm:max-w-sm my-10">
                <x-card rounded="rounded-none sm:rounded-lg">
                    {{ $slot }}
                </x-card>
            </div>

            <!-- <div class="mt-8 flex justify-center px-0">
                <div class="text-center text-neutral-50">
                    <div class="flex items-center gap-4">
                        <a href="https://gitlab.com/tenten8401" class="group inline-flex items-center">
                            Made with <x-icon name="heart" class="mx-1 h-5 w-5" /> by Isaac A.
                        </a>
                    </div>
                </div>
            </div> -->
        </div>
    </div>
</body>

</html>
