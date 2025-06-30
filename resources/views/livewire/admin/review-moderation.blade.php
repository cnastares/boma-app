<div class="space-y-6">
    {{-- Encabezado y Estadísticas --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Moderación de Reseñas</h1>
            <button wire:click="loadStats" class="btn btn-secondary btn-sm">
                <x-heroicon-o-arrow-path class="w-4 h-4 mr-2" />
                Actualizar
            </button>
        </div>

        {{-- Grid de Estadísticas --}}
        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-8 gap-4">
            <div class="bg-yellow-50 dark:bg-yellow-900/20 p-4 rounded-lg">
                <div class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">{{ $stats['pending_reviews'] ?? 0 }}</div>
                <div class="text-sm text-yellow-600 dark:text-yellow-400">Pendientes</div>
            </div>
            
            <div class="bg-red-50 dark:bg-red-900/20 p-4 rounded-lg">
                <div class="text-2xl font-bold text-red-600 dark:text-red-400">{{ $stats['flagged_reviews'] ?? 0 }}</div>
                <div class="text-sm text-red-600 dark:text-red-400">Flaggeadas</div>
            </div>
            
            <div class="bg-orange-50 dark:bg-orange-900/20 p-4 rounded-lg">
                <div class="text-2xl font-bold text-orange-600 dark:text-orange-400">{{ $stats['pending_reports'] ?? 0 }}</div>
                <div class="text-sm text-orange-600 dark:text-orange-400">Reportes</div>
            </div>
            
            <div class="bg-red-50 dark:bg-red-900/20 p-4 rounded-lg">
                <div class="text-2xl font-bold text-red-600 dark:text-red-400">{{ $stats['urgent_reports'] ?? 0 }}</div>
                <div class="text-sm text-red-600 dark:text-red-400">Urgentes</div>
            </div>
            
            <div class="bg-green-50 dark:bg-green-900/20 p-4 rounded-lg">
                <div class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $stats['today_approved'] ?? 0 }}</div>
                <div class="text-sm text-green-600 dark:text-green-400">Aprobadas Hoy</div>
            </div>
            
            <div class="bg-red-50 dark:bg-red-900/20 p-4 rounded-lg">
                <div class="text-2xl font-bold text-red-600 dark:text-red-400">{{ $stats['today_rejected'] ?? 0 }}</div>
                <div class="text-sm text-red-600 dark:text-red-400">Rechazadas Hoy</div>
            </div>
            
            <div class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-lg">
                <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $stats['average_resolution_time'] ?? 0 }}h</div>
                <div class="text-sm text-blue-600 dark:text-blue-400">Tiempo Prom.</div>
            </div>
            
            <div class="bg-purple-50 dark:bg-purple-900/20 p-4 rounded-lg">
                <div class="text-2xl font-bold text-purple-600 dark:text-purple-400">{{ $stats['backlog_age'] ?? 0 }}</div>
                <div class="text-sm text-purple-600 dark:text-purple-400">Días Backlog</div>
            </div>
        </div>
    </div>

    {{-- Filtros y Búsqueda --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-4">
            {{-- Búsqueda --}}
            <div class="lg:col-span-2">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Buscar</label>
                <input type="text" wire:model.live.debounce.300ms="searchTerm" 
                       placeholder="Buscar por contenido, usuario..." 
                       class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700">
            </div>

            {{-- Estado --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Estado</label>
                <select wire:model.live="statusFilter" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700">
                    <option value="all">Todos</option>
                    <option value="pending">Pendientes</option>
                    <option value="approved">Aprobadas</option>
                    <option value="rejected">Rechazadas</option>
                    <option value="flagged">Flaggeadas</option>
                    <option value="needs_attention">Necesita Atención</option>
                </select>
            </div>

            {{-- Prioridad --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Prioridad</label>
                <select wire:model.live="priorityFilter" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700">
                    <option value="all">Todas</option>
                    <option value="urgent">Urgente</option>
                    <option value="high">Alta</option>
                    <option value="medium">Media</option>
                    <option value="low">Baja</option>
                </select>
            </div>

            {{-- Moderador --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Moderador</label>
                <select wire:model.live="moderatorFilter" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700">
                    <option value="all">Todos</option>
                    <option value="unassigned">Sin Asignar</option>
                    @foreach($moderators as $moderator)
                        <option value="{{ $moderator['id'] }}">{{ $moderator['name'] }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Acciones --}}
            <div class="flex items-end">
                <button wire:click="resetFilters" class="btn btn-secondary w-full">
                    <x-heroicon-o-x-mark class="w-4 h-4 mr-2" />
                    Limpiar
                </button>
            </div>
        </div>
    </div>

    {{-- Pestañas --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
        <div class="border-b border-gray-200 dark:border-gray-700">
            <nav class="flex space-x-8 px-6" x-data="{ activeTab: 'reviews' }">
                <button @click="activeTab = 'reviews'" 
                        :class="activeTab === 'reviews' ? 'border-primary-500 text-primary-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    Reseñas ({{ $reviews->total() }})
                </button>
                <button @click="activeTab = 'reports'" 
                        :class="activeTab === 'reports' ? 'border-primary-500 text-primary-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    Reportes ({{ $reports->total() }})
                </button>
            </nav>
        </div>

        {{-- Contenido de Pestañas --}}
        <div class="p-6" x-data="{ activeTab: 'reviews' }">
            {{-- Pestaña de Reseñas --}}
            <div x-show="activeTab === 'reviews'" x-transition>
                @if($reviews->count() > 0)
                    <div class="space-y-4">
                        @foreach($reviews as $review)
                            <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-6 hover:shadow-md transition-shadow">
                                <div class="flex justify-between items-start mb-4">
                                    <div class="flex items-start space-x-4">
                                        {{-- Avatar del usuario --}}
                                        <div class="flex-shrink-0">
                                            <div class="w-10 h-10 bg-gray-300 rounded-full flex items-center justify-center">
                                                <span class="text-gray-600 font-medium">
                                                    {{ substr($review->user->name, 0, 1) }}
                                                </span>
                                            </div>
                                        </div>

                                        {{-- Información de la reseña --}}
                                        <div class="flex-1">
                                            <div class="flex items-center space-x-2 mb-2">
                                                <h3 class="font-medium text-gray-900 dark:text-white">{{ $review->user->name }}</h3>
                                                <span class="px-2 py-1 text-xs rounded-full bg-{{ $review->moderation_status_color }}-100 text-{{ $review->moderation_status_color }}-800">
                                                    {{ $review->moderation_status_text }}
                                                </span>
                                                @if($review->reported_count > 0)
                                                    <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800">
                                                        {{ $review->reported_count }} reporte(s)
                                                    </span>
                                                @endif
                                            </div>

                                            {{-- Rating --}}
                                            <div class="flex items-center space-x-2 mb-2">
                                                <div class="flex">
                                                    @for($i = 1; $i <= 5; $i++)
                                                        <x-heroicon-s-star class="w-4 h-4 {{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-300' }}" />
                                                    @endfor
                                                </div>
                                                <span class="text-sm text-gray-600">{{ $review->rating }}/5</span>
                                            </div>

                                            {{-- Contenido de la reseña --}}
                                            <p class="text-gray-700 dark:text-gray-300 mb-3">{{ $review->feedback }}</p>

                                            {{-- Estadísticas --}}
                                            <div class="flex items-center space-x-4 text-sm text-gray-500">
                                                <span>{{ $review->created_at->diffForHumans() }}</span>
                                                @if($review->helpful_count > 0 || $review->not_helpful_count > 0)
                                                    <span>{{ $review->helpfulness_score }}% útil ({{ $review->helpful_count + $review->not_helpful_count }} votos)</span>
                                                @endif
                                                @if($review->moderated_by)
                                                    <span>Moderado por {{ $review->moderatedBy->name }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Acciones --}}
                                    <div class="flex space-x-2">
                                        @if($review->needsModeration())
                                            <button wire:click="openModerationModal({{ $review->id }})" 
                                                    class="btn btn-primary btn-sm">
                                                <x-heroicon-o-cog-6-tooth class="w-4 h-4 mr-1" />
                                                Moderar
                                            </button>
                                        @endif

                                        @if($review->reports->count() > 0)
                                            <button class="btn btn-warning btn-sm">
                                                <x-heroicon-o-exclamation-triangle class="w-4 h-4 mr-1" />
                                                Ver Reportes ({{ $review->reports->count() }})
                                            </button>
                                        @endif
                                    </div>
                                </div>

                                {{-- Notas del administrador --}}
                                @if($review->admin_notes)
                                    <div class="mt-4 p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                        <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Notas del moderador:</p>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">{{ $review->admin_notes }}</p>
                                    </div>
                                @endif
                            </div>
                        @endforeach

                        {{-- Paginación --}}
                        <div class="mt-6">
                            {{ $reviews->links() }}
                        </div>
                    </div>
                @else
                    <div class="text-center py-8">
                        <x-heroicon-o-chat-bubble-left-ellipsis class="w-12 h-12 text-gray-400 mx-auto mb-4" />
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No hay reseñas</h3>
                        <p class="text-gray-500">No se encontraron reseñas con los filtros aplicados.</p>
                    </div>
                @endif
            </div>

            {{-- Pestaña de Reportes --}}
            <div x-show="activeTab === 'reports'" x-transition>
                @if($reports->count() > 0)
                    <div class="space-y-4">
                        @foreach($reports as $report)
                            <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-6">
                                <div class="flex justify-between items-start">
                                    <div class="flex-1">
                                        <div class="flex items-center space-x-2 mb-2">
                                            <span class="px-2 py-1 text-xs rounded-full bg-{{ $report->priority_color }}-100 text-{{ $report->priority_color }}-800">
                                                {{ ucfirst($report->priority) }}
                                            </span>
                                            <span class="px-2 py-1 text-xs rounded-full bg-{{ $report->status_color }}-100 text-{{ $report->status_color }}-800">
                                                {{ $report->status_text }}
                                            </span>
                                            @if($report->isOverdue())
                                                <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800">
                                                    Vencido
                                                </span>
                                            @endif
                                        </div>

                                        <h3 class="font-medium text-gray-900 dark:text-white mb-2">{{ $report->reason_text }}</h3>
                                        
                                        @if($report->description)
                                            <p class="text-gray-700 dark:text-gray-300 mb-3">{{ $report->description }}</p>
                                        @endif

                                        <div class="text-sm text-gray-500 space-y-1">
                                            <p>Reportado por {{ $report->reporter->name }} {{ $report->created_at->diffForHumans() }}</p>
                                            @if($report->assigned_to)
                                                <p>Asignado a {{ $report->assignedTo->name }}</p>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="flex space-x-2">
                                        @if($report->status === 'pending')
                                            <button wire:click="openReportModal({{ $report->id }})" 
                                                    class="btn btn-primary btn-sm">
                                                Resolver
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach

                        {{-- Paginación de reportes --}}
                        <div class="mt-6">
                            {{ $reports->links() }}
                        </div>
                    </div>
                @else
                    <div class="text-center py-8">
                        <x-heroicon-o-flag class="w-12 h-12 text-gray-400 mx-auto mb-4" />
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No hay reportes</h3>
                        <p class="text-gray-500">No se encontraron reportes con los filtros aplicados.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Modal de Moderación --}}
    @if($showModerationModal && $selectedReview)
        <x-filament::modal id="moderation-modal" width="2xl">
            <x-slot name="heading">
                Moderar Reseña
            </x-slot>

            <div class="space-y-4">
                {{-- Información de la reseña --}}
                <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                    <div class="flex items-center space-x-2 mb-2">
                        <h3 class="font-medium">{{ $selectedReview->user->name }}</h3>
                        <div class="flex">
                            @for($i = 1; $i <= 5; $i++)
                                <x-heroicon-s-star class="w-4 h-4 {{ $i <= $selectedReview->rating ? 'text-yellow-400' : 'text-gray-300' }}" />
                            @endfor
                        </div>
                    </div>
                    <p class="text-gray-700 dark:text-gray-300">{{ $selectedReview->feedback }}</p>
                </div>

                {{-- Formulario de moderación --}}
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Acción</label>
                        <select wire:model="moderationAction" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700">
                            <option value="">Seleccionar acción...</option>
                            <option value="approve">Aprobar</option>
                            <option value="reject">Rechazar</option>
                            <option value="flag">Marcar para revisión</option>
                        </select>
                        @error('moderationAction') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Razón</label>
                        <select wire:model="moderationReason" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700">
                            <option value="">Seleccionar razón...</option>
                            @foreach($reasonOptions as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('moderationReason') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Notas (opcional)</label>
                        <textarea wire:model="moderationNotes" rows="3" 
                                  class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700"
                                  placeholder="Agregar notas adicionales..."></textarea>
                        @error('moderationNotes') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            <x-slot name="actions">
                <button wire:click="closeModerationModal" class="btn btn-secondary">
                    Cancelar
                </button>
                <button wire:click="moderateReview" class="btn btn-primary">
                    Confirmar Moderación
                </button>
            </x-slot>
        </x-filament::modal>
    @endif

    {{-- Modal de Resolución de Reportes --}}
    @if($showReportModal && $selectedReport)
        <x-filament::modal id="report-modal" width="2xl">
            <x-slot name="heading">
                Resolver Reporte
            </x-slot>

            <div class="space-y-4">
                {{-- Información del reporte --}}
                <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                    <h3 class="font-medium mb-2">{{ $selectedReport->reason_text }}</h3>
                    @if($selectedReport->description)
                        <p class="text-gray-700 dark:text-gray-300 mb-2">{{ $selectedReport->description }}</p>
                    @endif
                    <p class="text-sm text-gray-500">Reportado por {{ $selectedReport->reporter->name }}</p>
                </div>

                {{-- Formulario de resolución --}}
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Acción</label>
                        <select wire:model="reportResolution" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700">
                            <option value="">Seleccionar acción...</option>
                            @foreach($resolutionOptions as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('reportResolution') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Notas de resolución</label>
                        <textarea wire:model="reportNotes" rows="3" 
                                  class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700"
                                  placeholder="Explicar la decisión tomada..."></textarea>
                        @error('reportNotes') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            <x-slot name="actions">
                <button wire:click="closeReportModal" class="btn btn-secondary">
                    Cancelar
                </button>
                <button wire:click="dismissReport" class="btn btn-warning">
                    Descartar
                </button>
                <button wire:click="resolveReport" class="btn btn-primary">
                    Resolver
                </button>
            </x-slot>
        </x-filament::modal>
    @endif
</div>

{{-- Scripts para notificaciones --}}
@script
<script>
    $wire.on('review-moderated', (event) => {
        new FilamentNotification()
            .title('Éxito')
            .body(event.message)
            .success()
            .send();
    });

    $wire.on('moderation-error', (event) => {
        new FilamentNotification()
            .title('Error')
            .body(event.message)
            .danger()
            .send();
    });

    $wire.on('report-resolved', (event) => {
        new FilamentNotification()
            .title('Éxito')
            .body(event.message)
            .success()
            .send();
    });

    $wire.on('bulk-action-completed', (event) => {
        new FilamentNotification()
            .title('Acción completada')
            .body(event.message)
            .success()
            .send();
    });
</script>
@endscript