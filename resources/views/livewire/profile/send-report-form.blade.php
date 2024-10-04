<?php

use Livewire\Volt\Component;

new class extends Component {
    public string $reports = '';

    public function sendReports(): void
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
        <p class="mt-1 text-md text-gray-600">{{ __('Reports') }}</p>
    </header>

    <form wire:submit="sendMessage" class="mt-6 space-y-6">
        <div>
            <x-textarea
                label="Reports"
                wire:model="reports"
                placeholder="Your report here..."
                rows="5"
                inline />
        </div>

        <div class="flex items-center gap-4">
            <x-button primary type="submit">{{ __('Send') }}</x-button>
            <x-button primary type="submit">{{ __('Cancel') }}</x-button>
        </div>
    </form>
</section>
