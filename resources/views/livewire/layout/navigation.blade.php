<?php

use Livewire\Volt\Component;

new class extends Component {
    public function logout(): void
    {
        auth()
            ->guard('web')
            ->logout();

        session()->invalidate();
        session()->regenerateToken();

        $this->redirect('/', navigate: true);
    }
}; ?>

<nav class="border-b border-gray-100 bg-white">
    <!-- Primary Navigation Menu -->
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex h-16 justify-between">
            <div class="flex">
                <!-- Logo -->
                <div class="hidden shrink-0 items-center sm:flex">
                    <a href="/" wire:navigate>
                        <x-application-logo class="block h-9 w-auto fill-current text-gray-800" />
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="flex space-x-8 sm:-my-px sm:ml-10">
                    @auth
                        <x-nav-link :href="route('my-tags')" :active="request()->routeIs('my-tags')" wire:navigate>
                            <x-icon name="globe" class="h-5 w-5 sm:me-1" />My<span
                                  class="hidden sm:ms-1 sm:block">Tags</span>
                        </x-nav-link>
                    @endauth
                    @auth
                    @if(auth()->user()->role != 1)
                    <x-nav-link :href="route('create-tag')" :active="request()->routeIs('create-tag')" wire:navigate>
                        <x-icon name="pencil-alt" class="h-5 w-5 sm:me-1" />Create<span
                              class="hidden sm:ms-1 sm:block">Tag</span>
                    </x-nav-link>
                    @else
                    <x-nav-link :href="route('create-bulk-tags')" :active="request()->routeIs('create-bulk-tags')" wire:navigate>
                        <x-icon name="printer" class="h-5 w-5 sm:me-1" />Create<span
                              class="hidden sm:ms-1 sm:block">Bulk Tags</span>
                    </x-nav-link>
                    @endif
                    @endauth
                    <!-- <x-nav-link :href="route('scan-tag')" :active="request()->routeIs('scan-tag')" wire:navigate invisible>
                        <x-icon name="qrcode" class="h-5 w-5 sm:me-1" />Scan<span
                              class="hidden sm:ms-1 sm:block">Tag</span>
                    </x-nav-link> -->
                </div>
            </div>

            @guest
                <div class="flex space-x-8 sm:-my-px sm:ml-10">
                    <x-nav-link :href="route('login')" :active="request()->routeIs('login')" wire:navigate>
                        {{ __('Log In') }}
                    </x-nav-link>
                    <x-nav-link :href="route('register')" :active="request()->routeIs('register')" wire:navigate>
                        {{ __('Register') }}
                    </x-nav-link>
                </div>
            @endguest

            <!-- Settings Dropdown -->
            @auth
                <div class="ml-6 flex items-center">
                    @persist('nav-dropdown-wireui-bug-workaround')
                        <x-dropdown>
                            <x-slot name="trigger">
                                <x-button flat right-icon="chevron-down">
                                    <x-avatar xs :src="auth()->user()->gravatar" />
                                    <div class="hidden sm:block" x-data="{ name: '{{ auth()->user()->name }}' }" x-text="name"
                                         x-on:profile-updated.window="name = $event.detail.name"></div>
                                </x-button>
                            </x-slot>

                            <x-dropdown.item :label="__('Profile')" :href="route('profile')" wire:navigate />
                            <x-dropdown.item :label="__('Log Out')" wire:click="logout" />
                        </x-dropdown>
                    @endpersist
                </div>
            @endauth
        </div>
    </div>
</nav>
