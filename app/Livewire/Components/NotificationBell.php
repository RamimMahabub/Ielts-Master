<?php

namespace App\Livewire\Components;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class NotificationBell extends Component
{
    public function getUnreadNotificationsProperty()
    {
        return Auth::user()?->unreadNotifications ?? collect();
    }

    public function getReadNotificationsProperty()
    {
        $user = Auth::user();

        if (!$user) {
            return collect();
        }

        return $user->readNotifications()->latest()->limit(20)->get();
    }

    public function markAsRead(string $id)
    {
        $notification = Auth::user()->notifications()->findOrFail($id);
        $notification->markAsRead();
        $this->dispatch('notificationRead'); // For cross-component updates if needed
    }

    public function markAsUnread(string $id)
    {
        $notification = Auth::user()->notifications()->findOrFail($id);
        $notification->markAsUnread();
    }

    public function render()
    {
        return view('livewire.components.notification-bell');
    }
}
