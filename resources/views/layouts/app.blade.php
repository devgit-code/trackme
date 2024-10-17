<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name') }} - {{ $title ?? 'Page' }}</title>

    <link rel="icon" type="image/svg" href="/favicon.svg">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

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

<body class="font-manrope antialiased">
    <img class="custom-bg-fix" src="{{ Vite::asset('resources/images/about-bg.jpg') }}" alt="">
    <div>
        <livewire:layout.navigation />

        <!-- Page Heading -->
        @if (isset($header))
            <header class="bg-red shadow">
                <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endif

        <x-notifications position="top-center" />

        <!-- Page Content -->
        <div class="grid sm:grid-cols-5 ap-g2 ">
            <div class="col-span-2 hidden sm:block relative ">
                <p class="p-4 m-2 mt-10 rounded text-xl leading-9 indent-3 absolute inset-3 h-fit z-10 bg-gray-200/60 ">{{ __('msg.welcome') }}</p>
                <img class="h-full object-cover blur-sm absolute inset-0 z-0" src="{{ Vite::asset('resources/images/screen2.png') }}" alt="">
            </div>
            <main class="col-span-3 pb-6">
                {{ $slot }}
            </main>
        </div>

        <!-- <div class="my-4 flex justify-center px-0">
            <div class="text-center text-neutral-50">
                <div class="flex flex-col items-center">
                    <a href="https://gitlab.com/tenten8401" class="group inline-flex items-center">
                        Made with <x-icon name="heart" class="mx-1 h-5 w-5" /> by Isaac A.
                    </a>
                    <a href="javascript:window.location.href=atob('{{ base64_encode('mailto:isaac@isaacs.site') }}')"
                       class="group inline-flex items-center underline">
                        Reach out to me!
                    </a>
                </div>
            </div>
        </div> -->
    </div>
</body>
</html>
