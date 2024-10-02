<?php

use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Livewire\Volt\Component;

new class extends Component {
    public string $current_password = '';

    public string $password = '';

    public string $password_confirmation = '';

    public function updatePassword(): void
    {
        try {
            $validated = $this->validate([
                'current_password' => ['required', 'string', 'current_password'],
                'password' => ['required', 'string', Password::defaults(), 'confirmed'],
            ]);
        } catch (ValidationException $e) {
            $this->reset('current_password', 'password', 'password_confirmation');

            throw $e;
        }

        auth()
            ->user()
            ->update([
                'password' => Hash::make($validated['password']),
            ]);

        $this->reset('current_password', 'password', 'password_confirmation');

        $this->dispatch('password-updated');
    }
}; ?>

<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Update Password') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __('Ensure your account is using a long, random password to stay secure.') }}
        </p>
    </header>

    <form wire:submit="updatePassword" class="mt-6 space-y-6">
        <div>
            <x-input type="password" wire:model="current_password" :label="__('Current Password')" required
                     autocomplete="current-password" />
        </div>

        <div>
            <x-input type="password" wire:model="password" :label="__('New Password')" required autocomplete="new-password" />
        </div>

        <div>
            <x-input type="password" wire:model="password_confirmation" :label="__('Confirm Password')" required
                     autocomplete="new-password" />
        </div>

        <div class="flex items-center gap-4">
            <x-button primary type="submit">{{ __('Save') }}</x-button>

            @if (session()->has('message'))
                <div>
                    {{ session('message') }}
                </div>
            @endif
        </div>
    </form>
</section>
