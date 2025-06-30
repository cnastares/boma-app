<div class="max-w-4xl mx-auto p-6">
    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Notificaciones</h1>
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                @if($unreadCount > 0)
                    Tienes {{ $unreadCount }} notificación{{ $unreadCount !== 1 ? 'es' : '' }} sin leer
                @else
                    No tienes notificaciones sin leer
                @endif
            </p>
        </div>

        {{-- Acciones --}}
        <div class="flex items-center space-x-3">
            @if($unreadCount > 0)
                <button wire:click="markAllAsRead" 
                        class="inline-flex items-center px-3 py-2 text-sm font-medium text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300">
                    <x-heroicon-o-check-circle class="w-4 h-4 mr-1" />
                    Marcar todas como leídas
                </button>
            @endif

            <button wire:click="deleteAllRead" 
                    wire:confirm="¿Estás seguro de que quieres eliminar todas las notificaciones leídas?"
                    class="inline-flex items-center px-3 py-2 text-sm font-medium text-red-600 dark:text-red-400 hover:text-red-700 dark:hover:text-red-300">
                <x-heroicon-o-trash class="w-4 h-4 mr-1" />
                Eliminar leídas
            </button>
        </div>
    </div>

    {{-- Filtros --}}
    <div class="flex items-center space-x-4 mb-6 p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
        {{-- Filtro solo no leídas --}}
        <label class="flex items-center">
            <input type="checkbox" 
                   wire:model.live="showOnlyUnread" 
                   class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Solo no leídas</span>
        </label>

        {{-- Filtro por tipo --}}
        <div class="flex items-center space-x-2">
            <span class="text-sm text-gray-700 dark:text-gray-300">Filtrar por:</span>
            <select wire:model.live="filterType" 
                    class="text-sm rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-primary-500 focus:border-primary-500">
                <option value="all">Todas</option>
                <option value="review_response_received">Respuestas recibidas</option>
                <option value="response_interaction">Interacciones</option>
                <option value="review_moderation">Moderación</option>
            </select>
        </div>
    </div>

    {{-- Lista de Notificaciones --}}
    <div class="space-y-4">
        @forelse($notifications as $notification)
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden hover:shadow-md transition-shadow duration-200 {{ $notification->read_at ? 'opacity-75' : 'ring-2 ring-primary-100 dark:ring-primary-900' }}">
                <div class="p-6">
                    <div class="flex items-start justify-between">
                        {{-- Contenido principal --}}
                        <div class="flex items-start space-x-4 flex-1">
                            {{-- Icono --}}
                            <div class="flex-shrink-0">
                                <div class="w-10 h-10 rounded-full bg-{{ $this->getNotificationColor($notification->data) }}-100 dark:bg-{{ $this->getNotificationColor($notification->data) }}-900 flex items-center justify-center">
                                    <x-dynamic-component :component="'heroicon-o-' . $this->getNotificationIcon($notification->data)" 
                                                       class="w-5 h-5 text-{{ $this->getNotificationColor($notification->data) }}-600 dark:text-{{ $this->getNotificationColor($notification->data) }}-400" />
                                </div>
                            </div>

                            {{-- Contenido --}}
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between mb-1">
                                    <h3 class="text-sm font-medium text-gray-900 dark:text-white">
                                        {{ $notification->data['title'] ?? 'Notificación' }}
                                    </h3>
                                    @if(!$notification->read_at)
                                        <span class="inline-flex items-center px-2 py-1 text-xs font-medium bg-primary-100 text-primary-800 dark:bg-primary-900 dark:text-primary-200 rounded-full">
                                            Nuevo
                                        </span>
                                    @endif
                                </div>
                                
                                <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">
                                    {{ $notification->data['message'] ?? '' }}
                                </p>

                                {{-- Información adicional --}}
                                @if(isset($notification->data['data']))
                                    <div class="text-xs text-gray-500 dark:text-gray-500 space-y-1">
                                        @if(isset($notification->data['data']['response_preview']))
                                            <p class="italic">"{{ $notification->data['data']['response_preview'] }}"</p>
                                        @endif
                                        
                                        @if(isset($notification->data['data']['entity_name']))
                                            <p>En: {{ $notification->data['data']['entity_name'] }}</p>
                                        @endif
                                    </div>
                                @endif

                                <p class="text-xs text-gray-500 dark:text-gray-500 mt-2">
                                    {{ $notification->created_at->diffForHumans() }}
                                    @if($notification->read_at)
                                        · Leída {{ $notification->read_at->diffForHumans() }}
                                    @endif
                                </p>
                            </div>
                        </div>

                        {{-- Acciones --}}
                        <div class="flex items-center space-x-2 ml-4" x-data="{ showMenu: false }">
                            {{-- Botón Ver/Ir a --}}
                            @if($this->getNotificationUrl($notification->data) !== '#')
                                <a href="{{ $this->getNotificationUrl($notification->data) }}" 
                                   wire:click="markAsRead('{{ $notification->id }}')"
                                   class="inline-flex items-center px-3 py-1 text-xs font-medium text-primary-600 dark:text-primary-400 hover:text-primary-700 dark:hover:text-primary-300 bg-primary-50 dark:bg-primary-900/20 hover:bg-primary-100 dark:hover:bg-primary-900/30 rounded-lg transition-colors duration-200">
                                    <x-heroicon-o-arrow-top-right-on-square class="w-3 h-3 mr-1" />
                                    Ver
                                </a>
                            @endif

                            {{-- Menú de acciones --}}
                            <button @click="showMenu = !showMenu" 
                                    class="p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
                                <x-heroicon-o-ellipsis-vertical class="w-4 h-4" />
                            </button>

                            <div x-show="showMenu" 
                                 x-transition
                                 @click.away="showMenu = false"
                                 class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 z-10">
                                
                                @if(!$notification->read_at)
                                    <button wire:click="markAsRead('{{ $notification->id }}')" 
                                            @click="showMenu = false"
                                            class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-t-lg">
                                        <x-heroicon-o-check-circle class="w-4 h-4 inline mr-2" />
                                        Marcar como leída
                                    </button>
                                @endif
                                
                                <button wire:click="deleteNotification('{{ $notification->id }}')" 
                                        @click="showMenu = false"
                                        wire:confirm="¿Estás seguro de que quieres eliminar esta notificación?"
                                        class="w-full text-left px-4 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-b-lg">
                                    <x-heroicon-o-trash class="w-4 h-4 inline mr-2" />
                                    Eliminar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            {{-- Estado vacío --}}
            <div class="text-center py-12">
                <div class="w-16 h-16 bg-gray-100 dark:bg-gray-800 rounded-full flex items-center justify-center mx-auto mb-4">
                    <x-heroicon-o-bell-slash class="w-8 h-8 text-gray-400" />
                </div>
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">
                    @if($showOnlyUnread)
                        No hay notificaciones sin leer
                    @else
                        No hay notificaciones
                    @endif
                </h3>
                <p class="text-gray-600 dark:text-gray-400">
                    @if($showOnlyUnread)
                        ¡Genial! Estás al día con todas tus notificaciones.
                    @else
                        Las notificaciones aparecerán aquí cuando recibas respuestas o interacciones.
                    @endif
                </p>
            </div>
        @endforelse
    </div>

    {{-- Paginación --}}
    @if($notifications->hasPages())
        <div class="mt-8">
            {{ $notifications->links() }}
        </div>
    @endif
</div>

{{-- Scripts para notificaciones en tiempo real --}}
@script
<script>
    $wire.on('notification-read', (event) => {
        new FilamentNotification()
            .title('Notificación marcada como leída')
            .success()
            .send();
    });

    $wire.on('notification-deleted', (event) => {
        new FilamentNotification()
            .title('Notificación eliminada')
            .success()
            .send();
    });

    $wire.on('all-notifications-read', (event) => {
        new FilamentNotification()
            .title('Todas las notificaciones marcadas como leídas')
            .success()
            .send();
    });

    $wire.on('read-notifications-deleted', (event) => {
        new FilamentNotification()
            .title('Notificaciones leídas eliminadas')
            .success()
            .send();
    });

    $wire.on('unread-count-updated', (event) => {
        // Actualizar contador en otros componentes si es necesario
        window.dispatchEvent(new CustomEvent('notification-count-updated', {
            detail: { count: event.count }
        }));
    });
</script>
@endscript