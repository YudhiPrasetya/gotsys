<?php

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Session;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Validation\ValidationException;

new #[Layout('layouts::app')] class extends Component
{
    #[Validate('required|string')]
    public string $login = '';

    #[Validate('required|string')]
    public string $password = '';

    public bool $remember = false;

    public function authenticate(){
        $this->validate();

        $this->ensureIsNotRateLimited();

        // Check input type (username or email)
        $fieldType = filter_var($this->login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        $credentials = [
            $fieldType => $this->login,
            'password' => $this->password
        ];

        if(!Auth::attempt($credentials, $this->remember)){
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'login' => __('auth.failed')
            ]);
        }

        RateLimiter::clear($this->throttleKey());
        Session::regenerate();

        $this->redirectRoute('dashboard', navigate: true);

    }

    /**
     * Ensure the authentication request is not rate limited.
     */
    protected function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout(request()));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            // 'email' => __('auth.throttle', [
            'login' => __('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the authentication rate limiting throttle key.
     */
    protected function throttleKey(): string
    {
        // return Str::transliterate(Str::lower($this->email).'|'.request()->ip());
        return Str::transliterate(Str::lower($this->login).'|'.request()->ip());
    }
};
?>

<div class="bg-gray-50 rounded shadow-lg shadow-black px-6 py-6 flex flex-col gap-6">
    {{-- <x-auth-session-status class="text-center" :status="session('status')" /> --}}

    {{-- <form wire:submit="authenticate" class="flex flex-col gap-6"> --}}
            {{-- <x-input wire:model="login" icon="users" hint="Insert your username or email" required>
                <x-slot:label>
                    <span class="text-gray-200">Username or email</span>
                </x-slot:label>
            </x-input> --}}
    {{-- <x-card image="{{ asset('images/unlocked.jpg') }}" /> --}}

    <img src="{{ asset('images/unlocked.jpg') }}" alt="Unlocked" class="mx-auto h-64 w-64 rounded-full object-cover" />
    
    <x-input label="Username or email *" icon="users" hint="Insert your username or email" wire:model="login" />

    <x-password label="Password *" icon="lock-closed" hint="Insert your password" wire:model="password" />

    <x-checkbox label="Remember Me" wire:model="remember" />

    <x-button text="Login" icon="lock-open" block color="purple" wire:click="authenticate" loading="authenticate" spinner="wave" />
    {{-- </form> --}}
</div>
