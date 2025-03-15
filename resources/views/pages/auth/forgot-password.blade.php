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

        session()->flash("status", __("A reset link will be sent if the account exists."));
    }
}; ?>

<x-layouts.app></x-layouts.app>
