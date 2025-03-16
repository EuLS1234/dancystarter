<?php

use Illuminate\Auth\Events\Lockout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Volt\Component;
use function Laravel\Folio\{name, middleware};

name("login");
middleware("guest");

new class extends Component {
    #[Validate("required|string|email")]
    public string $email = "";

    #[Validate("required|string")]
    public string $password = "";

    public bool $remember = false;

    /**
     * Handle an incoming authentication request.
     */
    public function login(): void
    {
        $this->validate();

        $this->ensureIsNotRateLimited();

        if (! Auth::attempt(["email" => $this->email, "password" => $this->password], $this->remember)) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                "email" => __("auth.failed"),
            ]);
        }

        RateLimiter::clear($this->throttleKey());
        Session::regenerate();

        session_notify("success","Logged in successfully");

        $this->redirectIntended(default: route("home", absolute: false), navigate: true);

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
            "email" => __("auth.throttle", [
                "seconds" => $seconds,
                "minutes" => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the authentication rate limiting throttle key.
     */
    protected function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->email) . "|" . request()->ip());
    }
}; ?>

<x-layouts.app>
    <div class="size-full p-20 space-y-10">
        <h2 class="text-center">{{ __('Sign In') }}</h2>
        @volt
        <form wire:submit.prevent='login' class="w-full max-w-md mx-auto space-y-5">

            <div>
                <label for="email">{{ __('Email') }}</label>
                <input type="email" id="email" wire:model='email' placeholder="{{ __('Please enter your email') }}" required>
                @error('email')
                <small class="pl-0.5 text-danger">{{ $message }}</small>
                @enderror
            </div>

            <div>
                <label for="password">{{ __('Password') }}</label>
                <div x-data="{ showPassword: false }" class="relative">
                    <input x-bind:type="showPassword ? 'text' : 'password'" id="password" wire:model='password' placeholder="{{ __('Please enter your password') }}" required>
                    <button type="button"  x-on:click="showPassword = !showPassword" class="absolute right-2.5 top-1/2 -translate-y-1/2 text-on-surface dark:text-on-surface-dark" aria-label="{{ __('Show password') }}">
                        <svg x-show="!showPassword" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true" class="size-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                        </svg>
                        <svg x-show="showPassword" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true" class="size-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />
                        </svg>
                    </button>
                </div>
                @error('password')
                <small class="pl-0.5 text-danger">{{ $message }}</small>
                @enderror
                <div class="w-full text-right">
                    <a href="{{ route('password.request') }}" class="w-fit font-medium text-primary underline-offset-2 hover:underline focus:underline focus:outline-hidden dark:text-primaryDark" wire:navigate>{{ __('Forgot Password ?') }}</a>
                </div>

            </div>

            <label for="remember" class="flex items-center gap-2 text-sm font-medium text-on-surface dark:text-on-surface-dark has-checked:text-on-surface-strong dark:has-checked:text-on-surface-dark-strong has-disabled:cursor-not-allowed has-disabled:opacity-75">
                <div class="relative flex items-center">
                    <input id="remember" type="checkbox" class="before:content[''] peer relative size-4 appearance-none overflow-hidden rounded-none border border-outline bg-surface-alt before:absolute before:inset-0 checked:border-primary checked:before:bg-primary focus:outline-2 focus:outline-offset-2 focus:outline-outline-strong checked:focus:outline-primary active:outline-offset-0 disabled:cursor-not-allowed dark:border-outline-dark dark:bg-surface-dark-alt dark:checked:border-primary-dark dark:checked:before:bg-primary-dark dark:focus:outline-outline-dark-strong dark:checked:focus:outline-primary-dark" wire:model='remember'/>
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" aria-hidden="true" stroke="currentColor" fill="none" stroke-width="4" class="pointer-events-none invisible absolute left-1/2 top-1/2 size-3 -translate-x-1/2 -translate-y-1/2 text-on-primary peer-checked:visible dark:text-on-primary-dark">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/>
                    </svg>
                </div>
                <span>{{ __('Remember Me') }}</span>
            </label>

            <button class='btn-primary w-full'>{{ __('Login') }}</button>

            <p class="text-on-surface dark:text-on-surface-dark text-center">{{ __('No account yet?') }} <a href="{{route('register')}}" wire:navigate class="font-medium text-primary underline-offset-2 hover:underline focus:underline focus:outline-hidden dark:text-primary-dark">{{ __('Create an Account') }}</a>
            </p>
        </form>
        @endvolt
    </div>

</x-layouts.app>
