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
    <script defer data-domain="trackme.info" src="https://plausible.isaacs.site/js/script.js"></script>
    
</head>

<body class="font-sans antialiased">
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
        <main>
            {{ $slot }}
        </main>

        <div class="my-4 flex justify-center px-0">
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
        </div>
    </div>
</body>

</html>
