<div class="space-y-4">
    {{-- Estadísticas de Respuestas --}}
    @if($responseStats['total_responses'] > 0)
        <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4">
            <div class="flex items-center justify-between text-sm text-gray-600 dark:text-gray-400">
                <span>{{ $responseStats['total_responses'] }} respuesta(s)</span>
                @if($responseStats['avg_response_time_hours'])
                    <span>Tiempo promedio de respuesta: {{ $responseStats['avg_response_time_hours'] }}h</span>
                @endif
            </div>
        </div>
    @endif

    {{-- Botón para Responder --}}
    @if($canRespond && !$showResponseForm)
        <div class="flex justify-end">
            <button wire:click="toggleResponseForm" 
                    class="inline-flex items-center px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                <x-heroicon-o-chat-bubble-left class="w-4 h-4 mr-2" />
                Responder a esta reseña
            </button>
        </div>
    @endif

    {{-- Formulario de Respuesta --}}
    @if($showResponseForm)
        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Tu Respuesta</h3>
                <button wire:click="cancelResponse" 
                        class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <x-heroicon-o-x-mark class="w-5 h-5" />
                </button>
            </div>

            <form wire:submit="submitResponse">
                <div class="space-y-4">
                    <div>
                        <label for="responseContent" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Escribe tu respuesta
                        </label>
                        <textarea wire:model="responseContent" 
                                  id="responseContent"
                                  rows="4" 
                                  class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-primary-500 focus:border-primary-500"
                                  placeholder="Responde de manera constructiva y profesional..."></textarea>
                        @error('responseContent') 
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> 
                        @enderror
                    </div>

                    <div class="flex items-center justify-between">
                        <div class="text-sm text-gray-500 dark:text-gray-400">
                            <span wire:ignore>{{ strlen($responseContent ?? '') }}</span>/1000 caracteres
                        </div>
                        
                        <div class="flex space-x-3">
                            <button type="button" 
                                    wire:click="cancelResponse"
                                    class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg transition-colors duration-200">
                                Cancelar
                            </button>
                            <button type="submit" 
                                    wire:loading.attr="disabled"
                                    wire:target="submitResponse"
                                    class="px-4 py-2 text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 disabled:opacity-50 disabled:cursor-not-allowed rounded-lg transition-colors duration-200">
                                <span wire:loading.remove wire:target="submitResponse">Publicar Respuesta</span>
                                <span wire:loading wire:target="submitResponse" class="flex items-center">
                                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Publicando...
                                </span>
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    @endif

    {{-- Lista de Respuestas --}}
    @if($responses->count() > 0)
        <div class="space-y-4">
            @foreach($responses as $response)
                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-6 @if($response->id === $primaryResponse?->id) ring-2 ring-primary-200 dark:ring-primary-800 @endif">
                    {{-- Header de la Respuesta --}}
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex items-center space-x-3">
                            {{-- Avatar --}}
                            <div class="flex-shrink-0">
                                @if($response->user->profile_image)
                                    <img src="{{ $response->user->profile_image }}" 
                                         alt="{{ $response->user->name }}" 
                                         class="w-10 h-10 rounded-full">
                                @else
                                    <div class="w-10 h-10 bg-primary-500 rounded-full flex items-center justify-center">
                                        <span class="text-white font-medium text-sm">
                                            {{ substr($response->user->name, 0, 1) }}
                                        </span>
                                    </div>
                                @endif
                            </div>

                            {{-- Info del Usuario --}}
                            <div>
                                <div class="flex items-center space-x-2">
                                    <h4 class="font-medium text-gray-900 dark:text-white">{{ $response->user->name }}</h4>
                                    @if($response->id === $primaryResponse?->id)
                                        <span class="px-2 py-1 text-xs font-medium bg-primary-100 text-primary-800 dark:bg-primary-900 dark:text-primary-200 rounded-full">
                                            Propietario
                                        </span>
                                    @endif
                                    @if($response->isEdited())
                                        <span class="text-xs text-gray-500 dark:text-gray-400">(editado)</span>
                                    @endif
                                </div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    {{ $response->created_at->diffForHumans() }}
                                    @if($response->isEdited())
                                        · Editado {{ $response->last_edited_at->diffForHumans() }}
                                    @endif
                                </p>
                            </div>
                        </div>

                        {{-- Acciones del Usuario --}}
                        @auth
                            @if($response->user_id === auth()->id())
                                <div class="flex space-x-2" x-data="{ showMenu: false }">
                                    <button @click="showMenu = !showMenu" 
                                            class="p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
                                        <x-heroicon-o-ellipsis-vertical class="w-5 h-5" />
                                    </button>

                                    <div x-show="showMenu" 
                                         x-transition
                                         @click.away="showMenu = false"
                                         class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 z-10">
                                        @if($response->canUserEdit(auth()->user()))
                                            <button wire:click="startEdit({{ $response->id }})" 
                                                    @click="showMenu = false"
                                                    class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-t-lg">
                                                <x-heroicon-o-pencil class="w-4 h-4 inline mr-2" />
                                                Editar
                                            </button>
                                        @endif
                                        
                                        @if($response->canUserDelete(auth()->user()))
                                            <button wire:click="deleteResponse({{ $response->id }})" 
                                                    @click="showMenu = false"
                                                    wire:confirm="¿Estás seguro de que quieres eliminar esta respuesta?"
                                                    class="w-full text-left px-4 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-b-lg">
                                                <x-heroicon-o-trash class="w-4 h-4 inline mr-2" />
                                                Eliminar
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        @endauth
                    </div>

                    {{-- Contenido de la Respuesta --}}
                    @if($editingResponse && $editingResponse->id === $response->id)
                        {{-- Modo Edición --}}
                        <form wire:submit="saveEdit" class="space-y-4">
                            <div>
                                <textarea wire:model="editContent" 
                                          rows="4" 
                                          class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-primary-500 focus:border-primary-500"></textarea>
                                @error('editContent') 
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> 
                                @enderror
                            </div>
                            
                            <div class="flex space-x-3">
                                <button type="submit" 
                                        class="px-4 py-2 text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 rounded-lg">
                                    Guardar
                                </button>
                                <button type="button" 
                                        wire:click="cancelEdit"
                                        class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg">
                                    Cancelar
                                </button>
                            </div>
                        </form>
                    @else
                        {{-- Modo Visualización --}}
                        <div class="prose prose-sm max-w-none dark:prose-invert">
                            <p class="text-gray-700 dark:text-gray-300 leading-relaxed">{{ $response->response }}</p>
                        </div>
                    @endif

                    {{-- Interacciones y Estadísticas --}}
                    <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                        <div class="flex items-center justify-between">
                            {{-- Botones de Interacción --}}
                            @auth
                                @if($response->canUserInteract(auth()->user()))
                                    <div class="flex items-center space-x-4">
                                        {{-- Útil --}}
                                        <button wire:click="markResponseAsHelpful({{ $response->id }})"
                                                class="flex items-center space-x-1 text-sm {{ $this->hasUserInteracted($response->id, 'helpful') ? 'text-green-600 dark:text-green-400' : 'text-gray-500 dark:text-gray-400 hover:text-green-600 dark:hover:text-green-400' }} transition-colors duration-200">
                                            <x-heroicon-{{ $this->hasUserInteracted($response->id, 'helpful') ? 's' : 'o' }}-hand-thumb-up class="w-4 h-4" />
                                            <span>{{ $response->helpful_count }}</span>
                                        </button>

                                        {{-- No Útil --}}
                                        <button wire:click="markResponseAsNotHelpful({{ $response->id }})"
                                                class="flex items-center space-x-1 text-sm {{ $this->hasUserInteracted($response->id, 'not_helpful') ? 'text-red-600 dark:text-red-400' : 'text-gray-500 dark:text-gray-400 hover:text-red-600 dark:hover:text-red-400' }} transition-colors duration-200">
                                            <x-heroicon-{{ $this->hasUserInteracted($response->id, 'not_helpful') ? 's' : 'o' }}-hand-thumb-down class="w-4 h-4" />
                                            <span>{{ $response->not_helpful_count }}</span>
                                        </button>

                                        {{-- Reportar --}}
                                        <button x-data="{ showReportModal: false }" 
                                                @click="showReportModal = true"
                                                class="flex items-center space-x-1 text-sm text-gray-500 dark:text-gray-400 hover:text-red-600 dark:hover:text-red-400 transition-colors duration-200">
                                            <x-heroicon-o-flag class="w-4 h-4" />
                                            <span>Reportar</span>

                                            {{-- Modal de Reporte --}}
                                            <div x-show="showReportModal" 
                                                 x-transition.opacity 
                                                 class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
                                                 @click.self="showReportModal = false">
                                                <div class="bg-white dark:bg-gray-800 rounded-xl p-6 max-w-md w-full mx-4">
                                                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Reportar Respuesta</h3>
                                                    
                                                    <form @submit.prevent="$wire.reportResponse({{ $response->id }}, $refs.reason.value, $refs.description.value); showReportModal = false">
                                                        <div class="space-y-4">
                                                            <div>
                                                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Motivo</label>
                                                                <select x-ref="reason" required class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700">
                                                                    <option value="">Seleccionar motivo...</option>
                                                                    <option value="spam">Spam</option>
                                                                    <option value="inappropriate">Contenido inapropiado</option>
                                                                    <option value="harassment">Acoso</option>
                                                                    <option value="false_info">Información falsa</option>
                                                                    <option value="other">Otro</option>
                                                                </select>
                                                            </div>
                                                            
                                                            <div>
                                                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Descripción (opcional)</label>
                                                                <textarea x-ref="description" rows="3" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700" placeholder="Describe el problema..."></textarea>
                                                            </div>
                                                        </div>
                                                        
                                                        <div class="flex space-x-3 mt-6">
                                                            <button type="button" 
                                                                    @click="showReportModal = false"
                                                                    class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg">
                                                                Cancelar
                                                            </button>
                                                            <button type="submit" 
                                                                    class="px-4 py-2 text-sm font-medium text-white bg-red-600 hover:bg-red-700 rounded-lg">
                                                                Reportar
                                                            </button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </button>
                                    </div>
                                @else
                                    <div class="flex items-center space-x-4 text-sm text-gray-500 dark:text-gray-400">
                                        <span class="flex items-center space-x-1">
                                            <x-heroicon-o-hand-thumb-up class="w-4 h-4" />
                                            <span>{{ $response->helpful_count }}</span>
                                        </span>
                                        <span class="flex items-center space-x-1">
                                            <x-heroicon-o-hand-thumb-down class="w-4 h-4" />
                                            <span>{{ $response->not_helpful_count }}</span>
                                        </span>
                                    </div>
                                @endif
                            @else
                                <div class="flex items-center space-x-4 text-sm text-gray-500 dark:text-gray-400">
                                    <span class="flex items-center space-x-1">
                                        <x-heroicon-o-hand-thumb-up class="w-4 h-4" />
                                        <span>{{ $response->helpful_count }}</span>
                                    </span>
                                    <span class="flex items-center space-x-1">
                                        <x-heroicon-o-hand-thumb-down class="w-4 h-4" />
                                        <span>{{ $response->not_helpful_count }}</span>
                                    </span>
                                </div>
                            @endauth

                            {{-- Score de Utilidad --}}
                            @if($response->helpful_count > 0 || $response->not_helpful_count > 0)
                                <div class="text-sm text-gray-500 dark:text-gray-400">
                                    {{ $response->helpfulness_score }}% útil
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    {{-- Mensaje cuando no hay respuestas --}}
    @if($responses->count() === 0 && !$canRespond)
        <div class="text-center py-8 text-gray-500 dark:text-gray-400">
            <x-heroicon-o-chat-bubble-left-ellipsis class="w-12 h-12 mx-auto mb-4 text-gray-300 dark:text-gray-600" />
            <p class="text-sm">No hay respuestas aún.</p>
        </div>
    @endif
</div>

{{-- Scripts para notificaciones --}}
@script
<script>
    $wire.on('response-created', (event) => {
        new FilamentNotification()
            .title('Éxito')
            .body(event.message)
            .success()
            .send();
    });

    $wire.on('response-updated', (event) => {
        new FilamentNotification()
            .title('Actualizado')
            .body(event.message)
            .success()
            .send();
    });

    $wire.on('response-deleted', (event) => {
        new FilamentNotification()
            .title('Eliminado')
            .body(event.message)
            .success()
            .send();
    });

    $wire.on('interaction-success', (event) => {
        new FilamentNotification()
            .title('Interacción registrada')
            .body(event.message)
            .success()
            .send();
    });

    $wire.on('report-success', (event) => {
        new FilamentNotification()
            .title('Reporte enviado')
            .body(event.message)
            .success()
            .send();
    });

    $wire.on('error', (event) => {
        new FilamentNotification()
            .title('Error')
            .body(event.message)
            .danger()
            .send();
    });

    $wire.on('info', (event) => {
        new FilamentNotification()
            .title('Información')
            .body(event.message)
            .info()
            .send();
    });
</script>
@endscript