<?php

namespace App\Livewire\Review;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\Attributes\On;

class NotificationBadge extends Component
{
    public int $unreadCount = 0;
    public bool $showDropdown = false;
    public $recentNotifications = [];

    public function mount(): void
    {
        $this->updateNotifications();
    }

    public function render()
    {
        return view('livewire.review.notification-badge');
    }

    public function toggleDropdown(): void
    {
        $this->showDropdown = !$this->showDropdown;
        
        if ($this->showDropdown) {
            $this->updateNotifications();
        }
    }

    public function updateNotifications(): void
    {
        $user = Auth::user();
        
        if (!$user) {
            $this->unreadCount = 0;
            $this->recentNotifications = [];
            return;
        }

        // Obtener conteo de no leídas
        $this->unreadCount = $user->unreadNotifications()->count();

        // Obtener notificaciones recientes (últimas 5)
        $this->recentNotifications = $user->notifications()
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($notification) {
                return [
                    'id' => $notification->id,
                    'title' => $notification->data['title'] ?? 'Notificación',
                    'message' => $notification->data['message'] ?? '',
                    'type' => $notification->data['type'] ?? 'default',
                    'created_at' => $notification->created_at,
                    'read_at' => $notification->read_at,
                    'url' => $this->getNotificationUrl($notification->data)
                ];
            })
            ->toArray();
    }

    public function markAsRead(string $notificationId): void
    {
        $user = Auth::user();
        
        if (!$user) {
            return;
        }

        $notification = $user->notifications()->find($notificationId);
        
        if ($notification && !$notification->read_at) {
            $notification->markAsRead();
            $this->updateNotifications();
        }
    }

    public function markAllAsRead(): void
    {
        $user = Auth::user();
        
        if (!$user) {
            return;
        }

        $user->unreadNotifications->markAsRead();
        $this->updateNotifications();
        
        $this->dispatch('success', message: 'Todas las notificaciones marcadas como leídas.');
    }

    public function goToNotificationCenter(): void
    {
        $this->showDropdown = false;
        return redirect()->route('notifications.index');
    }

    private function getNotificationUrl(array $notificationData): string
    {
        $data = $notificationData['data'] ?? [];
        
        return match($notificationData['type'] ?? '') {
            'review_response_received' => $data['entity_url'] ?? '#',
            'response_interaction' => route('user.responses') ?? '#',
            'review_moderation' => route('user.reviews') ?? '#',
            default => '#'
        };
    }

    public function getNotificationIcon(string $type): string
    {
        return match($type) {
            'review_response_received' => 'chat-bubble-left',
            'response_interaction' => 'hand-thumb-up',
            'review_moderation' => 'shield-check',
            'review_approved' => 'check-circle',
            'review_rejected' => 'x-circle',
            default => 'bell'
        };
    }

    public function getNotificationColor(string $type): string
    {
        return match($type) {
            'review_response_received' => 'blue',
            'response_interaction' => 'green',
            'review_moderation' => 'yellow',
            'review_approved' => 'green',
            'review_rejected' => 'red',
            default => 'gray'
        };
    }

    // ==================== EVENTOS ====================

    #[On('new-notification')]
    public function onNewNotification(): void
    {
        $this->updateNotifications();
    }

    #[On('notification-read')]
    public function onNotificationRead(): void
    {
        $this->updateNotifications();
    }

    #[On('user-logged-in')]
    public function onUserLoggedIn(): void
    {
        $this->updateNotifications();
    }

    #[On('user-logged-out')]
    public function onUserLoggedOut(): void
    {
        $this->unreadCount = 0;
        $this->recentNotifications = [];
        $this->showDropdown = false;
    }
}