<?php

use Livewire\Volt\Component;

new class extends Component {
    public string $subject = '';

    public string $content = '';

    public function sendMessage(): void
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
        <p class="mt-1 text-md text-gray-600">{{ __('You will be sending an anonymous email to: Mattah') }}</p>
    </header>

    <form wire:submit="sendMessage" class="mt-6 space-y-6">
        <div>
            <x-input wire:model="subject" :label="__('Subject')" required autofocus autocomplete="subject" />
        </div>
        <h2>Enter your message below</h2>
        <div>
            <x-textarea
                label="Message"
                wire:model="content"
                placeholder="Your message here..."
                rows="5"
                inline />
        </div>

        <div class="flex items-center gap-4">
            <x-button primary right-icon="chat-alt" type="submit">{{ __('Send') }}</x-button>

            @if (session()->has('message'))
                <div>
                    {{ session('message') }}
                </div>
            @endif
        </div>
    </form>
</section>
