<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use WireElements\LivewireStrict\LivewireStrict;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        LivewireStrict::lockProperties(components: [\Livewire\Volt\Component::class, 'App\Livewire/*']);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        VerifyEmail::toMailUsing(function (object $notifiable, string $url) {
            return (new MailMessage)
                ->subject('Verify Email Address')
                ->view('emails.auth.email-verify',[
                    'url' => $url,
                ]);
        });

        ResetPassword::toMailUsing(function (object $notifiable, string $token) {
            return (new MailMessage)
                ->subject('Reset Password')
                ->view('emails.auth.password-reset',[
                    'token' => $token,
                ]);
        });
    }
}
