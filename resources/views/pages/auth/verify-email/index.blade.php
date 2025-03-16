<?php

use App\Livewire\Actions\Logout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use function Laravel\Folio\{name, middleware};

name("verification.notice");
middleware("auth");

new class extends Component {
    /**
     * Send an email verification notification to the user.
     */
    public function sendVerification(): void
    {
        if (Auth::user()->hasVerifiedEmail()) {
            $this->redirectIntended(default: route("home", absolute: false), navigate: true);

            return;
        }

        Auth::user()->sendEmailVerificationNotification();

        $this->dispatch('notify', variant: 'success', message: __('Verification link sent'), title: '');
    }
};
?>

<x-layouts.app>
    <div class="size-full p-20 space-y-10">
        <div class="max-w-2xl mx-auto">
            <h2 class="text-center">{{ __('Verify Email Address') }}</h2>

            <div class="mt-6 bg-surface-alt p-6 rounded-lg shadow-sm dark:bg-surface-dark-alt">
                <p class="mb-4">{{ __('Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn\'t receive the email, we will gladly send you another.') }}</p>

                @if (session('status') == 'verification-link-sent')
                    <div class="bg-success/10 p-4 rounded-lg mb-6">
                        <p class="text-success dark:text-success-dark">
                            {{ __('A new verification link has been sent to the email address you provided during registration.') }}
                        </p>
                    </div>
                @endif

                @volt
                <div class="mt-6 flex items-center justify-between gap-4">
                    <button wire:click="sendVerification" class="btn-primary" wire:loading.attr="disabled">
                        {{ __('Resend Verification Email') }}
                    </button>

                    <livewire:auth.logout />
                </div>
                @endvolt
            </div>
        </div>
    </div>
</x-layouts.app>
