@props(['itemData' => []])
<?php

use Livewire\Volt\Component;

new class extends Component {

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
        <p class="mt-1 mb-4 text-xl text-gray-600">{{ __('Items List') }}</p>
    </header>
    @foreach($itemData as $item)
        <a href="{{ route('view-tag', ['uid' => $item->id]) }}" wire:key="{{ $item->id }}" wire:navigate>
            <div class="flex items-center justify-between border-t">
                <div>
                    <p class="mt-1 mb-4 text-md text-green-600">{{$item['name']}}</p>
                    <p class="mt-1 mb-4 text-md text-gray-600">{{$item['description']}}</p>
                </div>
                <x-button label="Follow" right-icon="eye" wire:click="delete(1)" primary/>
            </div>
        </a>
    @endforeach
</section>
