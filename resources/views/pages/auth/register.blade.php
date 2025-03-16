<?php

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use function Laravel\Folio\{name, middleware};

name("register");
middleware("guest");

new class extends Component {
    public string $name = "";
    public string $email = "";
    public string $password = "";
    public string $password_confirmation = "";

    /**
     * Handle an incoming registration request.
     */
    public function register(): void
    {
        $validated = $this->validate([
            "name" => ["required", "string", "max:255"],
            "email" => ["required", "string", "lowercase", "email", "max:255", "unique:" . User::class],
            "password" => ["required", "string", "confirmed", Rules\Password::defaults()],
        ]);

        $validated["password"] = Hash::make($validated["password"]);

        event(new Registered(($user = User::create($validated))));

        Auth::login($user);

        $this->redirectIntended(route("home", absolute: false), navigate: true);
    }
}; ?>

<x-layouts.app>
    <div class="size-full p-20 space-y-10">
        <h2 class="text-center">{{ __('Create Account') }}</h2>
        @volt
        <form wire:submit.prevent='register' class="w-full max-w-md mx-auto space-y-5">
            <div>
                <label for="name">{{ __('Name') }}</label>
                <input type="text" id="name" wire:model='name' placeholder="{{ __('Please enter your name') }}" required>
                @error('name')
                <small class="pl-0.5 text-danger">{{ $message }}</small>
                @enderror
            </div>

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
                    <button type="button" x-on:click="showPassword = !showPassword" class="absolute right-2.5 top-1/2 -translate-y-1/2 text-on-surface dark:text-on-surface-dark" aria-label="{{ __('Show password') }}">
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
            </div>

            <div>
                <label for="password_confirmation">{{ __('Confirm Password') }}</label>
                <div x-data="{ showPassword: false }" class="relative">
                    <input x-bind:type="showPassword ? 'text' : 'password'" id="password_confirmation" wire:model='password_confirmation' placeholder="{{ __('Please confirm your password') }}" required>
                    <button type="button" x-on:click="showPassword = !showPassword" class="absolute right-2.5 top-1/2 -translate-y-1/2 text-on-surface dark:text-on-surface-dark" aria-label="{{ __('Show password') }}">
                        <svg x-show="!showPassword" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true" class="size-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                        </svg>
                        <svg x-show="showPassword" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true" class="size-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />
                        </svg>
                    </button>
                </div>
                @error('password_confirmation')
                <small class="pl-0.5 text-danger">{{ $message }}</small>
                @enderror
            </div>

            <button wire:loading.disabled class='btn-primary w-full'>{{ __('Register') }}</button>

            <p class="text-on-surface dark:text-on-surface-dark text-center">
                {{ __('Already have an account?') }} <a href="{{route('login')}}" wire:navigate class="font-medium text-primary underline-offset-2 hover:underline focus:underline focus:outline-hidden dark:text-primary-dark">{{ __('Sign In') }}</a>
            </p>
        </form>
        @endvolt
    </div>
</x-layouts.app>
