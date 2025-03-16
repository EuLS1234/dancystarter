<?php

use Livewire\Attributes\Computed;
use Livewire\Volt\Component;

new class extends Component {
    #[Computed]
    public function notifications()
    {
        if (!auth()->check()) {
            return collect();
        }

        return auth()->user()->unreadNotifications;
    }

    public function markAsRead($notificationId)
    {
        $notification = auth()->user()->notifications()->findOrFail($notificationId);
        $notification->markAsRead();
        $this->dispatch('notification-read');
    }

    public function markAllAsRead()
    {
        auth()->user()->unreadNotifications->markAsRead();
        $this->dispatch('notifications-cleared');
    }
};
?>

<div class="relative w-fit ml-auto z-50 "
     x-data="{show_notifications: false}"
     x-cloak
     @notification-read.window="$wire.$refresh()"
     @notifications-cleared.window="show_notifications = false; $wire.$refresh()">
    <button @click="show_notifications = !show_notifications" class="flex items-center relative p-0.5 bg-primary-800 text-white hover:bg-secondary-800  rounded-full transition-colors">
        <svg class="w-6 h-6 sm:w-6 sm:h-6" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m10.827 5.465-.435-2.324m.435 2.324a5.338 5.338 0 0 1 6.033 4.333l.331 1.769c.44 2.345 2.383 2.588 2.6 3.761.11.586.22 1.171-.31 1.271l-12.7 2.377c-.529.099-.639-.488-.749-1.074C5.813 16.73 7.538 15.8 7.1 13.455c-.219-1.169.218 1.162-.33-1.769a5.338 5.338 0 0 1 4.058-6.221Zm-7.046 4.41c.143-1.877.822-3.461 2.086-4.856m2.646 13.633a3.472 3.472 0 0 0 6.728-.777l.09-.5-6.818 1.277Z"/>
        </svg>
        @if($this->notifications->isNotEmpty())
            <span class="absolute top-0 right-0 sm:-top-1 sm:-right-1 bg-red-500 text-white text-xxs font-bold rounded-full h-4 w-4 sm:h-4 sm:w-4 flex items-center justify-center">
                    {{ $this->notifications->count() }}
                </span>
        @endif
    </button>

    <div class="absolute w-60 md:w-96 p-1 right-2 -bottom-4 translate-y-full shadow-lg shadow-black bg-white z-20 rounded-lg max-h-96 overflow-y-auto" x-show="show_notifications" @click.outside="show_notifications=false">
        <div>
            <div class="flex justify-between items-center p-2 border-b-2">
                @if($this->notifications->isNotEmpty())
                    <button class="btn-secondary !text-sm mt-2" wire:click="markAllAsRead">{{__('Mark All As Read')}}</button>
                @endif
                <a class="!text-sm btn-primary right-0 mt-0.5 ml-auto" href="#">{{__('View all')}}</a>
            </div>
            @forelse($this->notifications as $notification)
                <div class="bg-secondary-100 text-sm p-4 m-2">
                    <p class="text-black font-semibold">{{ $notification->data['message'] }}</p>

                    <button class="btn-ghost-primary !text-xs mt-1" wire:click="markAsRead('{{ $notification->id }}')">
                        {{__('Mark as read')}}
                    </button>
                </div>
            @empty
                <div class="w-full text-center py-2">{{__('No Unread Notifications')}}</div>
            @endforelse
        </div>
    </div>
</div>
