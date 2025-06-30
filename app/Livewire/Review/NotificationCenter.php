<?php

namespace App\Livewire\Review;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\WithPagination;

class NotificationCenter extends Component
{
    use WithPagination;

    public bool $showOnlyUnread = false;
    public string $filterType = 'all';
    public int $unreadCount = 0;

    protected $listeners = [
        'notificationRead' => 'updateUnreadCount',
        'markAllAsRead' => 'markAllNotificationsAsRead'
    ];

    public function mount(): void
    {
        $this->updateUnreadCount();
    }

    public function render()
    {
        $notifications = $this->getNotifications();
        
        return view('livewire.review.notification-center', [
            'notifications' => $notifications
        ]);
    }

    public function getNotifications()
    {
        $user = Auth::user();
        
        if (!$user) {
            return collect();
        }

        $query = $user->notifications();

        // Filtrar por tipo si se especifica
        if ($this->filterType !== 'all') {
            $query->where('data->type', $this->filterType);
        }

        // Filtrar solo no leídas si se especifica
        if ($this->showOnlyUnread) {
            $query->whereNull('read_at');
        }

        return $query->orderBy('created_at', 'desc')
                    ->paginate(15);
    }

    public function toggleFilter(): void
    {
        $this->showOnlyUnread = !$this->showOnlyUnread;
        $this->resetPage();
    }

    public function setFilterType(string $type): void
    {
        $this->filterType = $type;
        $this->resetPage();
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
            $this->updateUnreadCount();
            
            $this->dispatch('notification-read', id: $notificationId);
        }
    }

    public function markAllAsRead(): void
    {
        $user = Auth::user();
        
        if (!$user) {
            return;
        }

        $user->unreadNotifications->markAsRead();
        $this->updateUnreadCount();
        
        $this->dispatch('all-notifications-read');
        $this->dispatch('info', message: 'Todas las notificaciones han sido marcadas como leídas.');
    }

    public function deleteNotification(string $notificationId): void
    {
        $user = Auth::user();
        
        if (!$user) {
            return;
        }

        $notification = $user->notifications()->find($notificationId);
        
        if ($notification) {
            $notification->delete();
            $this->updateUnreadCount();
            
            $this->dispatch('notification-deleted', id: $notificationId);
            $this->dispatch('success', message: 'Notificación eliminada.');
        }
    }

    public function deleteAllRead(): void
    {
        $user = Auth::user();
        
        if (!$user) {
            return;
        }

        $deletedCount = $user->readNotifications()->delete();
        
        $this->dispatch('read-notifications-deleted');
        $this->dispatch('success', message: "Se eliminaron {$deletedCount} notificaciones leídas.");
    }

    public function updateUnreadCount(): void
    {
        $user = Auth::user();
        
        if (!$user) {
            $this->unreadCount = 0;
            return;
        }

        $this->unreadCount = $user->unreadNotifications()->count();
        
        // Emitir evento para actualizar otros componentes
        $this->dispatch('unread-count-updated', count: $this->unreadCount);
    }

    public function getNotificationIcon(array $notificationData): string
    {
        return match($notificationData['type'] ?? '') {
            'review_response_received' => 'chat-bubble-left',
            'response_interaction' => 'hand-thumb-up',
            'review_moderation' => 'shield-check',
            'review_approved' => 'check-circle',
            'review_rejected' => 'x-circle',
            default => 'bell'
        };
    }

    public function getNotificationColor(array $notificationData): string
    {
        return match($notificationData['type'] ?? '') {
            'review_response_received' => 'blue',
            'response_interaction' => 'green',
            'review_moderation' => 'yellow',
            'review_approved' => 'green',
            'review_rejected' => 'red',
            default => 'gray'
        };
    }

    public function getNotificationUrl(array $notificationData): string
    {
        $data = $notificationData['data'] ?? [];
        
        return match($notificationData['type'] ?? '') {
            'review_response_received' => $data['entity_url'] ?? '#',
            'response_interaction' => route('user.responses') ?? '#',
            'review_moderation' => route('user.reviews') ?? '#',
            default => '#'
        };
    }

    // ==================== EVENTOS ====================

    #[On('new-notification')]
    public function onNewNotification(): void
    {
        $this->updateUnreadCount();
    }

    #[On('user-logged-in')]
    public function onUserLoggedIn(): void
    {
        $this->updateUnreadCount();
    }
}