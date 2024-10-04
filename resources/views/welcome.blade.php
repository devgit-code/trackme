@php
    use App\Models\Tag;
    use App\Models\Ping;
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('app.name') }}</title>

    <link rel="icon" type="image/svg" href="/favicon.svg">

    <!-- Scripts -->
    @wireUiScripts()
    @livewireStyles()
    @livewireScriptConfig
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer data-domain="trackme.info" src="https://plausible.isaacs.site/js/script.js"></script>
</head>

<body class="antialiased">
    <img class="custom-bg-fix" src="{{ Vite::asset('resources/images/snowy-cherokee.webp') }}" alt="">
    <div class="w-full min-h-screen backdrop-blur-sm">
        <div class="container mx-auto pt-4 filter-none xl:max-w-5xl">
            <div class="flex justify-center py-6">
                <x-application-logo text="true" shadow class="block h-12 w-auto fill-current text-neutral-50" />
            </div>
            <x-card class="grid sm:grid-cols-2 gap-4" rounded="rounded-none sm:rounded-lg">
                <div class="sm:col-span-2 flex flex-col justify-center gap-2">
                    @auth
                        <h1 class="text-center text-xl font-medium">Welcome back
                            {{ explode(' ', auth()->user()->name)[0] }}!</h1>
                        <div class="flex flex-row flex-wrap gap-4 justify-center">
                            <x-button class="w-fit" right-icon="chevron-double-right" lg href="{{ route('scan-tag') }}"
                                label="{{ __('Scan QR Code') }}" wire:navigate />
                            <x-button class="w-fit" right-icon="chevron-double-right" lg href="{{ route('my-tags') }}"
                                label="{{ __('My Tags') }}" wire:navigate />
                        </div>
                    @endauth
                </div>
                <p class="prose">{{ __('msg.welcome') }}</p>
                @php 
                $latestPing = Ping::latest('created_at')->first();
                if($latestPing)
                {
                    $tag = Tag::find($latestPing->tag_id);
                    $sample_data = json_encode($tag->getLocations());
                }else
                {
                    $sample_data = '[[35.306389,-78.323889,"Isaac Alich\n4 days ago"], [35.53965,-82.55095,"John Smith\n2 weeks ago"], [36.085336,-80.241745,"Jane Doe\n1 month ago"]]'; 
                }
                @endphp
                <x-map class="h-[350px]" :locations="$sample_data" />

                <div class="sm:col-span-2">
                    @guest
                        <div class="flex flex-row flex-wrap gap-4 justify-center">
                            <x-button class="w-fit" icon="qrcode" lg href="{{ route('scan-tag') }}"
                                label="{{ __('Scan QR Code') }}" wire:navigate />
                            <x-button class="w-fit" right-icon="chevron-double-right" lg href="{{ route('login') }}"
                                label="{{ __('Log In') }}" wire:navigate />
                            <x-button class="w-fit" right-icon="chevron-double-right" lg href="{{ route('register') }}"
                                label="{{ __('Register') }}" wire:navigate />
                        </div>
                    @endguest
                </div>
            </x-card>
            <div class="flex justify-center mt-8 px-0">
                <div class="text-center text-neutral-50">
                    <div class="flex items-center gap-4">
                        <a href="https://gitlab.com/tenten8401" class="group inline-flex items-center">
                            Made with <x-icon name="heart" class="w-5 h-5 mx-1" /> by Isaac A.
                        </a>
                    </div>
                </div>
            </div>
        </div>
        {{-- <video class="fixed w-auto min-w-full min-h-full max-w-none" autoplay muted loop>
            <source src="http://a1.phobos.apple.com/us/r1000/000/Features/atv/AutumnResources/videos/b3-1.mov"
                type="video/mp4">
        </video> --}}
        {{-- <div class="hero-content grid max-w-5xl mx-auto p-0 sm:p-6 lg:p-8">
            <div class="flex justify-center pt-4">
                <x-application-logo text="true" class="block h-12 w-auto fill-current text-base-100" />
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-3 sm:rounded-lg overflow-hidden shadow-2xl">
                <div class="card sm:col-span-2 rounded-none bg-base-100 text-neutral">
                    <div class="card-body block">
                        <h1 class="text-xl font-bold mb-2">Welcome!</h1>
                        <p class="mb-6">TrackMe allows you to create trackable QR code tags that update their location
                            when scanned. You can stick them on things and watch them travel!</p>
                        <p>There are two types of tags:</p>
                        <div class="flex flex-col md:flex-row w-full">
                            <div class="flex-grow basis-1 sm:basis-1/2">
                                <strong>Traveller Tags:</strong>
                                <ul>
                                    <li>- Last location is public</li>
                                    <li>- Anyone can add comments and update the location</li>
                                </ul>
                                <a class="mt-5 btn btn-sm btn-outline">Create Tag</a>
                            </div>
                            <div class="divider md:divider-horizontal">OR</div>
                            <div class="flex-grow basis-1 sm:basis-1/2">
                                <strong>Lost and Found:</strong>
                                <ul>
                                    <li>- Last location is private</li>
                                    <li>- Comments are private and sent only to you</li>
                                </ul>
                                <a href="{{ route('create-tag') }}" class="mt-5 btn btn-sm btn-outline">Create Tag</a>
                            </div>
                        </div>
                        <div class="w-full join mt-8">
                            <input class="w-full input input-bordered border-neutral join-item" placeholder="Enter a Tag ID to Search" />
                            <button class="btn btn-outline input-neutral join-item">Search</button>
                        </div>
                    </div>
                </div>
                <div class="card rounded-none glass">
                    <div class="card-body px-0 text-base-100">
                        <h1 class="text-xl text-center">Leaderboard</h1>
                        <div class="overflow-x-auto">
                            <table class="table">
                                <!-- head -->
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th class="text-base-100">Name</th>
                                        <th class="text-base-100">Distance</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- row 1 -->
                                    <tr>
                                        <th>1</th>
                                        <td>Cy Ganderton</td>
                                        <td>10 mi.</td>
                                    </tr>
                                    <!-- row 2 -->
                                    <tr>
                                        <th>2</th>
                                        <td>Hart Hagerty</td>
                                        <td>10 mi.</td>
                                    </tr>
                                    <!-- row 3 -->
                                    <tr>
                                        <th>3</th>
                                        <td>Brice Swyre</td>
                                        <td>10 mi.</td>
                                    </tr>
                                    <!-- row 3 -->
                                    <tr>
                                        <th>4</th>
                                        <td>Brice Swyre</td>
                                        <td>10 mi.</td>
                                    </tr>
                                    <!-- row 3 -->
                                    <tr>
                                        <th>5</th>
                                        <td>Brice Swyre</td>
                                        <td>10 mi.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="join flex justify-center">
                            @auth
                                <a class="btn btn-outline text-base-100 hover:text-neutral hover:bg-base-100 hover:border-base-100 "
                                    href="{{ route('my-tags') }}" wire:navigate>{{ __('My Tags') }}</a>
                            @else
                                <a class="btn btn-outline text-base-100 hover:text-neutral hover:bg-base-100 hover:border-base-100 join-item"
                                    href="{{ route('login') }}" wire:navigate>{{ __('Log In') }}</a>
                                <a class="btn btn-outline text-base-100 hover:text-neutral hover:bg-base-100 hover:border-base-100  join-item"
                                    href="{{ route('register') }}" wire:navigate>{{ __('Register') }}</a>
                            @endauth
                        </div>
                    </div>
                </div>
            </div>
            <div class="flex justify-center mt-8 px-0">
                <div class="text-center text-base-100">
                    <div class="flex items-center gap-4">
                        <a href="https://github.com/tenten8401" class="group inline-flex items-center">
                            Made with
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke-width="1.5" class="-mt-px mx-1 w-5 h-5 stroke-base-100">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z" />
                            </svg>
                            by Isaac A.
                        </a>
                    </div>
                </div>
            </div>
        </div> --}}
    </div>
    @livewireScriptConfig
</body>

</html>
