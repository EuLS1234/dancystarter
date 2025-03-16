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
        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 sm:w-6 sm:h-6" viewBox="0 0 50 50"><g fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"><path stroke="#344054" d="M31.25 37.5h-12.5a6.25 6.25 0 0 0 12.5 0"/><path stroke="#306cfe" d="M40.375 29.958a4.42 4.42 0 0 1 1.292 3.125v0A4.417 4.417 0 0 1 37.25 37.5h-24.5a4.416 4.416 0 0 1-4.417-4.417v0a4.42 4.42 0 0 1 1.292-3.125l2.875-2.875V18.75A12.5 12.5 0 0 1 25 6.25v0a12.5 12.5 0 0 1 12.5 12.5v8.333z"/></g></svg>
        @if($this->notifications->isNotEmpty())
            <span class="absolute top-0 right-0 sm:-top-1 sm:-right-1 bg-red-500 text-white text-xxs font-bold rounded-full h-4 w-4 sm:h-4 sm:w-4 flex items-center justify-center">
                    {{ $this->notifications->count() }}
                </span>
        @endif
    </button>

    <div class="absolute w-60 md:w-96 p-1 right-2 -bottom-4 translate-y-full shadow-lg shadow-black bg-white z-20 rounded-lg max-h-96 overflow-y-auto bg-surface dark:divide-outline-dark dark:border-outline-dark dark:bg-surface-dark rounded-radius dark:text-white" x-show="show_notifications" @click.outside="show_notifications=false">
        <div>
            <div class="flex justify-between items-center p-2 border-b-2">
                @if($this->notifications->isNotEmpty())
                    <button class="btn-secondary !text-sm mt-2" wire:click="markAllAsRead">{{__('Mark All As Read')}}</button>
                @endif
                <a class="!text-sm btn-primary right-0 mt-0.5 ml-auto" href="#">{{__('View all')}}</a>
            </div>
            @forelse($this->notifications as $notification)
                <div class="bg-secondary-100 text-sm p-4 m-2">
                    <p class=" font-semibold">{{ $notification->data['message'] }}</p>

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
