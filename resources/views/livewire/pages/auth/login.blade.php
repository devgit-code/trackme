<?php

use App\Providers\RouteServiceProvider;
use App\Services\TwilioService;
use App\Mail\NotificationMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Rule;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component {
    #[Rule(['required', 'string', 'email'])]
    public string $email = '';

    #[Rule(['required', 'string'])]
    public string $password = '';

    #[Rule(['boolean'])]
    public bool $remember = true;

    public function login(): void
    {
        $this->validate();

        $this->ensureIsNotRateLimited();

        if (!auth()->attempt($this->only(['email', 'password'], $this->remember))) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'email' => trans('auth.failed'),
            ]);
        }

        RateLimiter::clear($this->throttleKey());

   
        // Resolve TwilioService using the service container
        // $twilioService = app(TwilioService::class);
        // $twilioService->sendSms('+18777804236', 'Test' . ' scanned');

        // //The email sending is done using the to method on the Mail facade
        // $data = [
        //     'subject' => 'New User Logined',
        //     'name' => 'Test',
        // ];

        // Mail::to('secretwink282@gmail.com')->send(new NotificationMail($data));

        session()->regenerate();
        $this->redirect(session('url.intended', RouteServiceProvider::HOME), navigate: true);
    }

    protected function ensureIsNotRateLimited(): void
    {
        if (!RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout(request()));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    protected function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->email) . '|' . request()->ip());
    }
}; ?>

<div>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form wire:submit="login">
        <div class="grid gap-4">
            <x-input wire:model="email" icon="user" label="{{ __('Email') }}" required autofocus
                placeholder="{{ __('john.smith@gmail.com') }}" autocomplete="username" />

            <x-inputs.password wire:model="password" type="password" icon="lock-open" label="{{ __('Password') }}" required
                placeholder="{{ __('********') }}" autocomplete="current-password" />

            <x-toggle label="{{ __('Remember Me') }}" wire:model.defer="remember" />

            <x-button class="w-full" type="submit" spinner primary label="{{ __('Log in') }}" />

            @if (Route::has('password.request'))
                <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                    href="{{ route('password.request') }}" wire:navigate>
                    {{ __('Forgot your password?') }}
                </a>
            @endif
        </div>
    </form>
</div>
