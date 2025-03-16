<?php

use Illuminate\Support\Facades\Password;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use function Laravel\Folio\{name, middleware};

name("password.request");
middleware("guest");

new class extends Component {
    public string $email = "";

    /**
     * Send a password reset link to the provided email address.
     */
    public function sendPasswordResetLink(): void
    {
        $this->validate([
            "email" => ["required", "string", "email"],
        ]);

        Password::sendResetLink($this->only("email"));

        $this->dispatch('notify', variant: 'success', message: __('A reset link will be sent if the account exists.'), title: '');
    }
}; ?>

<x-layouts.app>
    <div class="size-full p-20 space-y-10">
        <h2 class="text-center">{{ __('Forgot Password') }}</h2>

        @volt
        <form wire:submit.prevent='sendPasswordResetLink' class="w-full max-w-md mx-auto space-y-5">
            <p class="text-on-surface dark:text-on-surface-dark">
                {{ __('Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.') }}
            </p>

            <div>
                <label for="email">{{ __('Email') }}</label>
                <input type="email" id="email" wire:model='email' placeholder="{{ __('Please enter your email') }}" required>
                @error('email')
                <small class="pl-0.5 text-danger">{{ $message }}</small>
                @enderror
            </div>

            <button class='btn-primary w-full' wire:target="sendPasswordResetLink" wire:loading.disabled>
                {{ __('Email Password Reset Link') }}
            </button>

            <p class="text-on-surface dark:text-on-surface-dark text-center">
                <a href="{{ route('login') }}" wire:navigate class="font-medium text-primary underline-offset-2 hover:underline focus:underline focus:outline-hidden dark:text-primary-dark">
                    {{ __('Back to Login') }}
                </a>
            </p>
        </form>
        @endvolt
    </div>
</x-layouts.app>
