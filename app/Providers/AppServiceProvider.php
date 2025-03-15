<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use WireElements\LivewireStrict\LivewireStrict;

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
        //
    }
}
