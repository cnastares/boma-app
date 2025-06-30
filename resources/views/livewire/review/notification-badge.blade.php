<div class="relative" x-data="{ open: @entangle('showDropdown') }">
    {{-- Botón de notificaciones --}}
    <button @click="$wire.toggleDropdown()" 
            class="relative p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 focus:ring-2 focus:ring-primary-500 focus:outline-none transition-colors duration-200">
        
        <x-heroicon-o-bell class="w-6 h-6" />
        
        {{-- Badge de conteo --}}
        @if($unreadCount > 0)
            <span class="absolute -top-1 -right-1 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white bg-red-600 rounded-full min-w-[1.25rem]">
                {{ $unreadCount > 99 ? '99+' : $unreadCount }}
            </span>
        @endif
    </button>

    {{-- Dropdown de notificaciones --}}
    <div x-show="open"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         @click.away="open = false"
         class="absolute right-0 top-full mt-2 w-80 bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 z-50 max-h-96 overflow-hidden">
        
        {{-- Header --}}
        <div class="flex items-center justify-between p-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white">Notificaciones</h3>
            
            @if($unreadCount > 0)
                <button wire:click="markAllAsRead" 
                        class="text-sm text-primary-600 dark:text-primary-400 hover:text-primary-700 dark:hover:text-primary-300 font-medium">
                    Marcar todas como leídas
                </button>
            @endif
        </div>

        {{-- Lista de notificaciones --}}
        <div class="max-h-64 overflow-y-auto">
            @forelse($recentNotifications as $notification)
                <div class="p-4 hover:bg-gray-50 dark:hover:bg-gray-700 border-b border-gray-200 dark:border-gray-700 last:border-b-0 {{ $notification['read_at'] ? 'opacity-75' : '' }}">
                    <div class="flex items-start space-x-3">
                        {{-- Icono --}}
                        <div class="flex-shrink-0 mt-1">
                            <div class="w-8 h-8 rounded-full bg-{{ $this->getNotificationColor($notification['type']) }}-100 dark:bg-{{ $this->getNotificationColor($notification['type']) }}-900 flex items-center justify-center">
                                <x-dynamic-component :component="'heroicon-o-' . $this->getNotificationIcon($notification['type'])" 
                                                   class="w-4 h-4 text-{{ $this->getNotificationColor($notification['type']) }}-600 dark:text-{{ $this->getNotificationColor($notification['type']) }}-400" />
                            </div>
                        </div>

                        {{-- Contenido --}}
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between mb-1">
                                <p class="text-sm font-medium text-gray-900 dark:text-white truncate">
                                    {{ $notification['title'] }}
                                </p>
                                @if(!$notification['read_at'])
                                    <span class="w-2 h-2 bg-primary-600 rounded-full flex-shrink-0 ml-2"></span>
                                @endif
                            </div>
                            
                            <p class="text-sm text-gray-600 dark:text-gray-400 line-clamp-2">
                                {{ $notification['message'] }}
                            </p>
                            
                            <p class="text-xs text-gray-500 dark:text-gray-500 mt-1">
                                {{ \Carbon\Carbon::parse($notification['created_at'])->diffForHumans() }}
                            </p>

                            {{-- Acciones --}}
                            <div class="flex items-center space-x-2 mt-2">
                                @if($notification['url'] !== '#')
                                    <a href="{{ $notification['url'] }}" 
                                       wire:click="markAsRead('{{ $notification['id'] }}')"
                                       @click="open = false"
                                       class="text-xs text-primary-600 dark:text-primary-400 hover:text-primary-700 dark:hover:text-primary-300 font-medium">
                                        Ver
                                    </a>
                                @endif

                                @if(!$notification['read_at'])
                                    <button wire:click="markAsRead('{{ $notification['id'] }}')" 
                                            class="text-xs text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300">
                                        Marcar como leída
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                {{-- Estado vacío --}}
                <div class="p-8 text-center">
                    <div class="w-12 h-12 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-3">
                        <x-heroicon-o-bell-slash class="w-6 h-6 text-gray-400" />
                    </div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">No hay notificaciones</p>
                </div>
            @endforelse
        </div>

        {{-- Footer --}}
        @if(count($recentNotifications) > 0)
            <div class="p-3 border-t border-gray-200 dark:border-gray-700">
                <button wire:click="goToNotificationCenter" 
                        @click="open = false"
                        class="w-full text-center text-sm text-primary-600 dark:text-primary-400 hover:text-primary-700 dark:hover:text-primary-300 font-medium py-2">
                    Ver todas las notificaciones
                </button>
            </div>
        @endif
    </div>
</div>

{{-- Estilos adicionales para line-clamp --}}
<style>
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
</style>

{{-- Scripts --}}
@script
<script>
    // Actualizar notificaciones cada 30 segundos
    setInterval(() => {
        $wire.updateNotifications();
    }, 30000);

    // Escuchar eventos de nuevas notificaciones
    $wire.on('new-notification', () => {
        $wire.updateNotifications();
    });

    // Escuchar eventos globales de notificaciones
    window.addEventListener('notification-count-updated', (event) => {
        $wire.updateNotifications();
    });
</script>
@endscript