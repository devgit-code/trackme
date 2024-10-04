<?php

use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component {
    public string $name = '';

    public string $email = '';

    public string $password = '';

    public string $password_confirmation = '';

    public function register(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ]);

        $validated['password'] = Hash::make($validated['password']);

        event(new Registered(($user = User::create($validated))));

        auth()->login($user);
     
        // todo: redirect user to tag if using embedded

        $this->redirect(RouteServiceProvider::HOME, navigate: true);
    }
}; ?>

<div>
    <form wire:submit="register">
        <div class="grid gap-4">
            <x-input wire:model="name" label="{{ __('Name') }}" placeholder="{{ __('John Smith') }}" icon="user"
                type="text" required autofocus autocomplete="name" />
            <x-input wire:model="email" label="{{ __('Email') }}" placeholder="{{ __('john.smith@gmail.com') }}"
                icon="at-symbol" type="email" required autocomplete="name" />
            <x-input wire:model="phone" label="{{ __('Phone Number') }}" placeholder="{{ __('+') }}"
                icon="phone" type="string" autocomplete="name" />
            <x-input wire:model="password" label="{{ __('Password') }}" placeholder="{{ __('********') }}"
                icon="lock-closed" type="password" required autocomplete="name" />
            <x-input wire:model="password_confirmation" label="{{ __('Confirm Password') }}"
                placeholder="{{ __('********') }}" icon="lock-closed" type="password" required autocomplete="name" />
            <x-button class="w-full" type="submit" spinner primary label="{{ __('Register') }}" />
            <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                href="{{ route('login') }}" wire:navigate>
                {{ __('Already registered?') }}
            </a>
        </div>
    </form>
</div>
