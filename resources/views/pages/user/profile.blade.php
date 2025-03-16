<?php

use App\Models\User;
use App\Notifications\NewNotification;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Livewire\Volt\Component;
use function Laravel\Folio\{name, middleware};

name("user.profile");
middleware("auth");

new class extends Component {
    public string $name = '';
    public string $email = '';
    public string $current_password = '';
    public string $password = '';
    public string $password_confirmation = '';

    // For email verification
    public string $email_verification_password = '';
    public string $new_email = '';

    // For delete account
    public string $delete_password = '';

    // For other browser sessions logout
    public string $logout_other_sessions_password = '';

    /**
     * Mount the component.
     */
    public function mount(): void
    {
        $this->name = Auth::user()->name;
        $this->email = Auth::user()->email;
    }

    /**
     * Update the user's profile name.
     */
    public function updateName(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        Auth::user()->update($validated);

        Auth::user()->notify(new NewNotification($this->name));

//        Notification::send(Auth::user(), new NewNotification($this->name));

        // Dispatch notification to the toast notifier
        $this->dispatch('notify', variant: 'success', message: __('Profile name updated successfully.'), title: '');
    }

    /**
     * Initiate the change email process.
     */
    public function initiateEmailChange(): void
    {
        $validated = $this->validate([
            'new_email' => ['required', 'string', 'email', 'max:255', Rule::unique(User::class, 'email')],
            'email_verification_password' => ['required', 'string'],
        ]);

        // Verify the current password before proceeding
        if (!Hash::check($this->email_verification_password, Auth::user()->password)) {
            $this->addError('email_verification_password', __('The provided password does not match our records.'));
            return;
        }

        // Here you would typically:
        // 1. Store the new email in a temporary field or separate table
        // 2. Generate a unique token for verification
        // 3. Send verification email with the token
        // 4. Only update the email after verification

        // For demonstration, we'll just update directly (you should implement the steps above)
        Auth::user()->update([
            'email' => $this->new_email
        ]);

        $this->email = $this->new_email;
        $this->reset(['new_email', 'email_verification_password']);

        // Dispatch notification to the toast notifier
        $this->dispatch('notify', variant: 'success', message: __('Email Updated Successfully'), title: '');
    }

    /**
     * Update the user's password.
     */
    public function updatePassword(): void
    {
        $validated = $this->validate([
            'current_password' => ['required', 'string', 'current_password'],
            'password' => ['required', 'string', 'confirmed', 'min:8', 'different:current_password'],
        ]);

        Auth::user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        $this->reset(['current_password', 'password', 'password_confirmation']);

        // Dispatch notification to the toast notifier
        $this->dispatch('notify', variant: 'success', message: __('Password updated successfully.'), title: '');
    }

    /**
     * Log out from all other browser sessions.
     */
    public function logoutOtherBrowserSessions(): void
    {
        $this->validate([
            'logout_other_sessions_password' => ['required', 'string'],
        ]);

        // Verify the current password before proceeding
        if (!Hash::check($this->logout_other_sessions_password, Auth::user()->password)) {
            $this->addError('logout_other_sessions_password', __('The provided password does not match our records.'));
            return;
        }

        // Log out from other devices
        Auth::logoutOtherDevices($this->logout_other_sessions_password);

        $this->reset('logout_other_sessions_password');

        // Dispatch notification to the toast notifier
        $this->dispatch('notify', [
            'variant' => 'success',
            'title' => __('Success'),
            'message' => __('You have been logged out from all other browser sessions.')
        ]);
    }

    /**
     * Delete the user's account.
     */
    public function deleteAccount(): void
    {
        $this->validate([
            'delete_password' => ['required', 'string'],
        ]);

        // Verify the current password before proceeding
        if (!Hash::check($this->delete_password, Auth::user()->password)) {
            $this->addError('delete_password', __('The provided password does not match our records.'));
            return;
        }

        $user = Auth::user();

        // Logout
        Auth::logout();

        // Delete the user
        $user->delete();

        // Invalidate session
        Session::invalidate();
        Session::regenerateToken();

        // Redirect to home page
        $this->redirectRoute('home', navigate: true);
    }
}
?>

<x-layouts.app>
    <div class="size-full p-20 space-y-10">
        <h2 class="text-center">{{ __('Profile') }}</h2>

        <div class="max-w-2xl mx-auto space-y-12">
            <!-- Profile Information - Name -->
            @volt('profile.update-name')
            <div class="bg-surface-alt dark:bg-surface-dark-alt p-6 rounded-lg shadow-sm">
                <h3 class="text-lg font-semibold mb-4">{{ __('Profile Information') }}</h3>

                <p class="mb-4 text-on-surface-variant dark:text-on-surface-dark-variant">
                    {{ __('Update your account\'s profile name.') }}
                </p>

                <form wire:submit.prevent="updateName" class="space-y-5">
                    <div>
                        <label for="name">{{ __('Name') }}</label>
                        <input type="text" id="name" wire:model="name" placeholder="{{ __('Please enter your name') }}"
                               required>
                        @error('name')
                        <small class="pl-0.5 text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="flex justify-end">
                        <button class="btn-primary cursor-pointer" type="submit" wire:loading.disabled>
                            {{ __('Save') }}
                        </button>
                    </div>
                </form>
            </div>
            @endvolt

            <!-- Email Information -->
            @volt('profile.update-email')
            <div class="bg-surface-alt dark:bg-surface-dark-alt p-6 rounded-lg shadow-sm">
                <h3 class="text-lg font-semibold mb-4">{{ __('Email Address') }}</h3>

                <p class="mb-4 text-on-surface-variant dark:text-on-surface-dark-variant">
                    {{ __('Update your email address. For security reasons, please confirm your password.') }}
                </p>

                <div class="mb-4 p-4 bg-info/10 rounded-lg">
                    <h4 class="font-medium text-info">{{ __('Current Email') }}</h4>
                    <p>{{ $email }}</p>
                </div>

                <form wire:submit.prevent="initiateEmailChange" class="space-y-5">
                    <div>
                        <label for="new_email">{{ __('New Email Address') }}</label>
                        <input type="email" id="new_email" wire:model="new_email"
                               placeholder="{{ __('Please enter your new email address') }}" required>
                        @error('new_email')
                        <small class="pl-0.5 text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div>
                        <label for="email_verification_password">{{ __('Current Password') }}</label>
                        <div x-data="{ showPassword: false }" class="relative">
                            <input x-bind:type="showPassword ? 'text' : 'password'" id="email_verification_password"
                                   wire:model="email_verification_password"
                                   placeholder="{{ __('Please enter your current password') }}" required>
                            <button type="button" x-on:click="showPassword = !showPassword"
                                    class="absolute right-2.5 top-1/2 -translate-y-1/2 text-on-surface dark:text-on-surface-dark"
                                    aria-label="{{ __('Show password') }}">
                                <svg x-show="!showPassword" xmlns="http://www.w3.org/2000/svg" fill="none"
                                     viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true"
                                     class="size-5">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>
                                </svg>
                                <svg x-show="showPassword" xmlns="http://www.w3.org/2000/svg" fill="none"
                                     viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true"
                                     class="size-5">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88"/>
                                </svg>
                            </button>
                        </div>
                        @error('email_verification_password')
                        <small class="pl-0.5 text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="flex justify-end">
                        <button class="btn-primary cursor-pointer" wire:loading.disabled type="submit">
                            {{ __('Update Email') }}
                        </button>
                    </div>
                </form>
            </div>
            @endvolt

            <!-- Update Password -->
            @volt('profile.update-password')
            <div x-data="{ showMessage: false }"
                 class="bg-surface-alt dark:bg-surface-dark-alt p-6 rounded-lg shadow-sm">
                <h3 class="text-lg font-semibold mb-4">{{ __('Update Password') }}</h3>

                <p class="mb-4 text-on-surface-variant dark:text-on-surface-dark-variant">
                    {{ __('Ensure your account is using a long, random password to stay secure.') }}
                </p>

                <form wire:submit.prevent="updatePassword" class="space-y-5">
                    <div>
                        <label for="current_password">{{ __('Current Password') }}</label>
                        <div x-data="{ showPassword: false }" class="relative">
                            <input x-bind:type="showPassword ? 'text' : 'password'" id="current_password"
                                   wire:model="current_password"
                                   placeholder="{{ __('Please enter your current password') }}" required>
                            <button type="button" x-on:click="showPassword = !showPassword"
                                    class="absolute right-2.5 top-1/2 -translate-y-1/2 text-on-surface dark:text-on-surface-dark"
                                    aria-label="{{ __('Show password') }}">
                                <svg x-show="!showPassword" xmlns="http://www.w3.org/2000/svg" fill="none"
                                     viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true"
                                     class="size-5">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>
                                </svg>
                                <svg x-show="showPassword" xmlns="http://www.w3.org/2000/svg" fill="none"
                                     viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true"
                                     class="size-5">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88"/>
                                </svg>
                            </button>
                        </div>
                        @error('current_password')
                        <small class="pl-0.5 text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div>
                        <label for="password">{{ __('New Password') }}</label>
                        <div x-data="{ showPassword: false }" class="relative">
                            <input x-bind:type="showPassword ? 'text' : 'password'" id="password" wire:model="password"
                                   placeholder="{{ __('Please enter your new password') }}" required>
                            <button type="button" x-on:click="showPassword = !showPassword"
                                    class="absolute right-2.5 top-1/2 -translate-y-1/2 text-on-surface dark:text-on-surface-dark"
                                    aria-label="{{ __('Show password') }}">
                                <svg x-show="!showPassword" xmlns="http://www.w3.org/2000/svg" fill="none"
                                     viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true"
                                     class="size-5">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>
                                </svg>
                                <svg x-show="showPassword" xmlns="http://www.w3.org/2000/svg" fill="none"
                                     viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true"
                                     class="size-5">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88"/>
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
                            <input x-bind:type="showPassword ? 'text' : 'password'" id="password_confirmation"
                                   wire:model="password_confirmation"
                                   placeholder="{{ __('Please confirm your new password') }}" required>
                            <button type="button" x-on:click="showPassword = !showPassword"
                                    class="absolute right-2.5 top-1/2 -translate-y-1/2 text-on-surface dark:text-on-surface-dark"
                                    aria-label="{{ __('Show password') }}">
                                <svg x-show="!showPassword" xmlns="http://www.w3.org/2000/svg" fill="none"
                                     viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true"
                                     class="size-5">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>
                                </svg>
                                <svg x-show="showPassword" xmlns="http://www.w3.org/2000/svg" fill="none"
                                     viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true"
                                     class="size-5">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88"/>
                                </svg>
                            </button>
                        </div>
                        @error('password_confirmation')
                        <small class="pl-0.5 text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="flex justify-end">
                        <button class="btn-primary cursor-pointer" wire:loading.disabled type="submit">
                            {{ __('Update Password') }}
                        </button>
                    </div>

                    <!-- Success Message -->
                    <div
                        x-show="showMessage"
                        x-init="
                                window.addEventListener('password-updated', () => {
                                    showMessage = true;
                                    setTimeout(() => showMessage = false, 3000)
                                })
                            "
                        x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0 transform -translate-y-2"
                        x-transition:enter-end="opacity-100 transform translate-y-0"
                        x-transition:leave="transition ease-in duration-300"
                        x-transition:leave-start="opacity-100 transform translate-y-0"
                        x-transition:leave-end="opacity-0 transform -translate-y-2"
                        class="bg-success/10 p-4 rounded-lg mt-4"
                        style="display: none;"
                    >
                        <p class="text-success dark:text-success-dark">
                            {{ __('Password updated successfully.') }}
                        </p>
                    </div>
                </form>
            </div>
            @endvolt

            <!-- Logout from all other sessions -->
            @volt('profile.logout-sessions')
            <div x-data="{ confirmingLogout: false }"
                 class="bg-surface-alt dark:bg-surface-dark-alt p-6 rounded-lg shadow-sm">
                <h3 class="text-lg font-semibold mb-4">{{ __('Browser Sessions') }}</h3>

                <p class="mb-4 text-on-surface-variant dark:text-on-surface-dark-variant">
                    {{ __('Manage and logout from your active sessions on other browsers and devices.') }}
                </p>

                <p class="mb-4 text-on-surface-variant dark:text-on-surface-dark-variant">
                    {{ __('If necessary, you may log out of all of your other browser sessions across all of your devices. If you feel your account has been compromised, you should also update your password.') }}
                </p>

                <div class="flex justify-end">
                    <button type="button" wire:navigate wire:loading.disabled class="btn-warning cursor-pointer"
                            @click="confirmingLogout = true">
                        {{ __('Logout from Other Sessions') }}
                    </button>
                </div>

                <!-- Modal for confirmation -->
                <div x-cloak x-show="confirmingLogout" class="fixed inset-0 z-50 overflow-y-auto"
                     aria-labelledby="modal-title" role="dialog" aria-modal="true">
                    <div class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                        <!-- Background overlay -->
                        <div x-show="confirmingLogout" x-transition:enter="ease-out duration-300"
                             x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                             x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100"
                             x-transition:leave-end="opacity-0"
                             class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75"
                             @click="confirmingLogout = false" aria-hidden="true"></div>

                        <!-- Modal panel -->
                        <div x-show="confirmingLogout" x-transition:enter="ease-out duration-300"
                             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                             x-transition:leave="ease-in duration-200"
                             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                             class="inline-block px-4 pt-5 pb-4 overflow-hidden text-left align-bottom transition-all transform bg-surface dark:bg-surface-dark rounded-lg shadow-xl sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                            <div class="sm:flex sm:items-start">
                                <div
                                    class="flex items-center justify-center flex-shrink-0 w-12 h-12 mx-auto rounded-full bg-warning/10 sm:mx-0 sm:h-10 sm:w-10">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-warning" fill="none"
                                         viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                    </svg>
                                </div>
                                <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                    <h3 class="text-lg font-medium leading-6" id="modal-title">
                                        {{ __('Logout Other Browser Sessions') }}
                                    </h3>
                                    <div class="mt-2">
                                        <p class="text-sm text-on-surface-variant dark:text-on-surface-dark-variant">
                                            {{ __('Please enter your password to confirm you would like to log out of your other browser sessions across all of your devices.') }}
                                        </p>

                                        <div class="mt-4">
                                            <input type="password" wire:model="logout_other_sessions_password"
                                                   class="w-full" placeholder="{{ __('Password') }}"/>

                                            @error('logout_other_sessions_password')
                                            <small class="pl-0.5 text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                                <button type="button" wire:loading.disabled
                                        class="btn-warning cursor-pointer w-full sm:ml-3 sm:w-auto"
                                        wire:click="logoutOtherBrowserSessions">
                                    {{ __('Logout Other Browser Sessions') }}
                                </button>
                                <button type="button"
                                        class="btn-on-warning mt-3 w-full sm:mt-0 sm:w-auto cursor-pointer"
                                        @click="confirmingLogout = false">
                                    {{ __('Cancel') }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endvolt

            <!-- Delete Account -->
            @volt('profile.delete-account')
            <div x-data="{ confirmingDelete: false }"
                 class="bg-danger/5 dark:bg-danger-dark/5 p-6 rounded-lg shadow-sm">
                <h3 class="text-lg font-semibold mb-4 text-danger dark:text-danger-dark">{{ __('Delete Account') }}</h3>

                <p class="mb-4 text-on-surface-variant dark:text-on-surface-dark-variant">
                    {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.') }}
                </p>

                <div class="flex justify-end">
                    <button type="button" class="btn-danger cursor-pointer" @click="confirmingDelete = true">
                        {{ __('Delete Account') }}
                    </button>
                </div>

                <!-- Modal for confirmation -->
                <div x-cloak x-show="confirmingDelete" class="fixed inset-0 z-50 overflow-y-auto"
                     aria-labelledby="modal-title" role="dialog" aria-modal="true">
                    <div class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                        <!-- Background overlay -->
                        <div x-show="confirmingDelete" x-transition:enter="ease-out duration-300"
                             x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                             x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100"
                             x-transition:leave-end="opacity-0"
                             class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75"
                             @click="confirmingDelete = false" aria-hidden="true"></div>

                        <!-- Modal panel -->
                        <div x-show="confirmingDelete" x-transition:enter="ease-out duration-300"
                             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                             x-transition:leave="ease-in duration-200"
                             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                             class="inline-block px-4 pt-5 pb-4 overflow-hidden text-left align-bottom transition-all transform bg-surface dark:bg-surface-dark rounded-lg shadow-xl sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                            <div class="sm:flex sm:items-start">
                                <div
                                    class="flex items-center justify-center flex-shrink-0 w-12 h-12 mx-auto rounded-full bg-danger/10 sm:mx-0 sm:h-10 sm:w-10">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-danger" fill="none"
                                         viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                    </svg>
                                </div>
                                <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                    <h3 class="text-lg font-medium leading-6 text-danger" id="modal-title">
                                        {{ __('Delete Account') }}
                                    </h3>
                                    <div class="mt-2">
                                        <p class="text-sm text-on-surface-variant dark:text-on-surface-dark-variant">
                                            {{ __('Are you sure you want to delete your account? Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.') }}
                                        </p>

                                        <div class="mt-4">
                                            <input type="password" wire:model="delete_password" class="w-full"
                                                   placeholder="{{ __('Password') }}"/>

                                            @error('delete_password')
                                            <small class="pl-0.5 text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                                <button type="button" wire:loading.disabled
                                        class="btn-danger cursor-pointer w-full sm:ml-3 sm:w-auto"
                                        wire:click="deleteAccount">
                                    {{ __('Delete Account') }}
                                </button>
                                <button type="button"
                                        class="btn-on-warning cursor-pointer mt-3 w-full sm:mt-0 sm:w-auto"
                                        @click="confirmingDelete = false">
                                    {{ __('Cancel') }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endvolt
        </div>
    </div>
</x-layouts.app>
