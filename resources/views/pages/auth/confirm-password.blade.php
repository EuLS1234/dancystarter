<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

use function Laravel\Folio\{name, middleware};

name("password.confirm");
middleware("auth");

new class extends Component {
    public string $password = "";

    /**
     * Confirm the current user's password.
     */
    public function confirmPassword(): void
    {
        $this->validate([
            "password" => ["required", "string"],
        ]);

        if (
            ! Auth::guard("web")->validate([
                "email" => Auth::user()->email,
                "password" => $this->password,
            ])
        ) {
            throw ValidationException::withMessages([
                "password" => __("auth.password"),
            ]);
        }

        session(["auth.password_confirmed_at" => time()]);

        $this->redirectIntended(default: route("home", absolute: false), navigate: true);
    }
}; ?>

<x-layouts.app></x-layouts.app>
