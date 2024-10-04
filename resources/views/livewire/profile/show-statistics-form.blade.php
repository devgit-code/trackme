<?php
use App\Models\User;
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
        <h2 class="text-lg text-center font-medium text-gray-900 py-5">
            {{ __('Statistics') }}
        </h2>

    </header>
    <div class="flex justify-between gap-x-6 py-2 px-5">
        <div class="min-w-0 gap-x-4">
            <p class="mt-1 text-md text-gray-600">{{ __('Bills Entered: 8792') }}</p>
            <p class="mt-1 text-md text-gray-600">{{ __('Bills with Hits: 326') }}</p>
            <p class="mt-1 text-md text-gray-600">{{ __('Hit Rate: 3.71%') }}</p>
            <p class="mt-1 text-md text-gray-600">{{ __('Total Hits: 376') }}</p>
        </div>
        <div class="min-w-0 gap-x-4">
            <p class="mt-1 text-md text-gray-600">{{ __('George Score: 0.00') }}</p>
            <p class="mt-1 text-md text-gray-600">{{ __('Days of Inactivity: 2558') }}</p>
        </div>
    </div>
    <div class="justify-center px-32 py-6">
        <div class="gap-x-4">
            <p class="mt-1 text-md text-gray-600">{{ __('City, State: Shawano County, WI') }}</p>
            <p class="mt-1 text-md text-gray-600">{{ __('Age: 35') }}</p>
            <p class="mt-1 text-md text-gray-600">{{ __('Peopel Generally consider me: a Family') }}</p>
            <p class="mt-1 text-md text-gray-600">{{ __('E-mail address: ') }}{{auth()->user()->email}}</p>
            <p class="mt-1 text-md text-gray-600">{{ __('Social Networking:') }}</p>
        </div>
    </div>
</section>
