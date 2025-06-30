<div class="bg-white rounded-lg shadow-sm border">
    <div class="p-6">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-semibold text-gray-900">
                Reseñas y Calificaciones
            </h3>
            <div class="flex items-center space-x-2">
                <div class="flex items-center">
                    @for ($i = 1; $i <= 5; $i++)
                        <svg class="w-4 h-4 {{ $i <= $averageRating ? 'text-yellow-400' : 'text-gray-300' }}" 
                             fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                    @endfor
                </div>
                <span class="text-sm text-gray-600">
                    {{ number_format($averageRating, 1) }} ({{ $totalReviews }} reseñas)
                </span>
            </div>
        </div>

        @if (session()->has('success'))
            <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-md">
                <p class="text-green-800">{{ session('success') }}</p>
            </div>
        @endif

        @if (session()->has('error'))
            <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-md">
                <p class="text-red-800">{{ session('error') }}</p>
            </div>
        @endif

        <!-- Formulario para nueva reseña de cliente -->
        @if ($this->canUserLeaveReview())
            <div class="mb-6 border-b pb-6">
                @if (!$showReviewForm)
                    <button wire:click="toggleReviewForm" 
                            class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Escribir una reseña
                    </button>
                @else
                    <div class="space-y-4">
                        <h4 class="font-medium text-gray-900">Deja tu reseña</h4>
                        
                        <!-- Rating selector -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Calificación</label>
                            <div class="flex items-center space-x-1">
                                @for ($i = 1; $i <= 5; $i++)
                                    <button type="button" 
                                            wire:click="$set('newReview.rating', {{ $i }})"
                                            class="text-2xl focus:outline-none transition-colors {{ $i <= $newReview['rating'] ? 'text-yellow-400' : 'text-gray-300 hover:text-yellow-300' }}">
                                        ★
                                    </button>
                                @endfor
                                <span class="ml-2 text-sm text-gray-600">({{ $newReview['rating'] }} estrella{{ $newReview['rating'] !== 1 ? 's' : '' }})</span>
                            </div>
                        </div>

                        <!-- Comment -->
                        <div>
                            <label for="newReview.comment" class="block text-sm font-medium text-gray-700 mb-2">
                                Comentario
                            </label>
                            <textarea wire:model="newReview.comment" 
                                      rows="4" 
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                                      placeholder="Describe tu experiencia..."></textarea>
                            @error('newReview.comment') 
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p> 
                            @enderror
                        </div>

                        <!-- Buttons -->
                        <div class="flex space-x-3">
                            <button wire:click="submitReview" 
                                    class="px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-md hover:bg-green-700 transition-colors">
                                Enviar Reseña
                            </button>
                            <button wire:click="toggleReviewForm" 
                                    class="px-4 py-2 bg-gray-300 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-400 transition-colors">
                                Cancelar
                            </button>
                        </div>
                    </div>
                @endif
            </div>
        @endif

        <!-- Lista de reseñas de clientes -->
        @if ($clientReviews->count() > 0)
            <div class="space-y-6">
                <h4 class="font-medium text-gray-900">Reseñas de Clientes</h4>
                
                @foreach ($clientReviews as $review)
                    <div class="border rounded-lg p-4">
                        <div class="flex items-start justify-between mb-3">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-gray-300 rounded-full flex items-center justify-center">
                                    <span class="text-sm font-medium text-gray-700">
                                        {{ substr($review->reviewer->name, 0, 1) }}
                                    </span>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900">{{ $review->reviewer->name }}</p>
                                    <div class="flex items-center space-x-2">
                                        <div class="flex">
                                            @for ($i = 1; $i <= 5; $i++)
                                                <svg class="w-4 h-4 {{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-300' }}" 
                                                     fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                </svg>
                                            @endfor
                                        </div>
                                        <span class="text-sm text-gray-500">{{ $review->created_at->diffForHumans() }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <p class="text-gray-700 mb-4">{{ $review->comment }}</p>

                        <!-- Respuesta del proveedor -->
                        @if ($review->response)
                            <div class="bg-gray-50 rounded-lg p-4 border-l-4 border-blue-500">
                                <div class="flex items-center space-x-2 mb-2">
                                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
                                    </svg>
                                    <span class="text-sm font-medium text-blue-900">Respuesta del Proveedor</span>
                                    <span class="text-sm text-gray-500">{{ $review->response->created_at->diffForHumans() }}</span>
                                </div>
                                <p class="text-gray-800 mb-2">{{ $review->response->response_text }}</p>
                                
                                @if ($review->response->client_rating)
                                    <div class="flex items-center space-x-2">
                                        <span class="text-sm text-gray-600">Calificación al cliente:</span>
                                        <div class="flex">
                                            @for ($i = 1; $i <= 5; $i++)
                                                <svg class="w-3 h-3 {{ $i <= $review->response->client_rating ? 'text-yellow-400' : 'text-gray-300' }}" 
                                                     fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                </svg>
                                            @endfor
                                        </div>
                                        <span class="text-sm text-gray-600">({{ $review->response->client_rating }}/5)</span>
                                    </div>
                                @endif
                            </div>
                        @else
                            <!-- Formulario de respuesta para el proveedor -->
                            @if ($review->canBeRespondedBy(auth()->user()))
                                @if (!($showResponseForm[$review->id] ?? false))
                                    <button wire:click="toggleResponseForm('{{ $review->id }}')" 
                                            class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                        Responder
                                    </button>
                                @else
                                    <div class="bg-blue-50 rounded-lg p-4 mt-4 space-y-4">
                                        <h5 class="font-medium text-blue-900">Responder a la reseña</h5>
                                        
                                        <!-- Respuesta -->
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Respuesta</label>
                                            <textarea wire:model="newResponse.{{ $review->id }}.response_text" 
                                                      rows="3" 
                                                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                                                      placeholder="Escribe tu respuesta..."></textarea>
                                            @error("newResponse.{$review->id}.response_text") 
                                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p> 
                                            @enderror
                                        </div>

                                        <!-- Calificación al cliente (opcional) -->
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Calificar al cliente (opcional)</label>
                                            <div class="flex items-center space-x-1">
                                                @for ($i = 1; $i <= 5; $i++)
                                                    <button type="button" 
                                                            wire:click="$set('newResponse.{{ $review->id }}.client_rating', {{ $i }})"
                                                            class="text-lg focus:outline-none transition-colors {{ $i <= ($newResponse[$review->id]['client_rating'] ?? 0) ? 'text-yellow-400' : 'text-gray-300 hover:text-yellow-300' }}">
                                                        ★
                                                    </button>
                                                @endfor
                                                <button type="button" 
                                                        wire:click="$set('newResponse.{{ $review->id }}.client_rating', null)"
                                                        class="ml-2 text-xs text-gray-500 hover:text-gray-700">
                                                    Limpiar
                                                </button>
                                            </div>
                                        </div>

                                        <!-- Buttons -->
                                        <div class="flex space-x-3">
                                            <button wire:click="submitResponse('{{ $review->id }}')" 
                                                    class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 transition-colors">
                                                Enviar Respuesta
                                            </button>
                                            <button wire:click="toggleResponseForm('{{ $review->id }}')" 
                                                    class="px-4 py-2 bg-gray-300 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-400 transition-colors">
                                                Cancelar
                                            </button>
                                        </div>
                                    </div>
                                @endif
                            @endif
                        @endif
                    </div>
                @endforeach
            </div>
        @endif

        <!-- Reseñas del proveedor a clientes -->
        @if ($providerReviews->count() > 0)
            <div class="mt-8 pt-6 border-t space-y-6">
                <h4 class="font-medium text-gray-900">Reseñas del Proveedor a Clientes</h4>
                
                @foreach ($providerReviews as $review)
                    <div class="border rounded-lg p-4 bg-amber-50 border-amber-200">
                        <div class="flex items-start justify-between mb-3">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-amber-300 rounded-full flex items-center justify-center">
                                    <svg class="w-5 h-5 text-amber-800" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900">{{ $review->reviewer->name }} (Proveedor)</p>
                                    <div class="flex items-center space-x-2">
                                        <div class="flex">
                                            @for ($i = 1; $i <= 5; $i++)
                                                <svg class="w-4 h-4 {{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-300' }}" 
                                                     fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                </svg>
                                            @endfor
                                        </div>
                                        <span class="text-sm text-gray-500">{{ $review->created_at->diffForHumans() }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <p class="text-gray-700">{{ $review->comment }}</p>
                        <p class="text-sm text-amber-700 mt-2">
                            Reseña para: {{ $review->reviewed->name }}
                        </p>
                    </div>
                @endforeach
            </div>
        @endif

        @if ($clientReviews->count() === 0 && $providerReviews->count() === 0)
            <div class="text-center py-8">
                <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-3.582 8-8 8a8.013 8.013 0 01-7-4L5 20l.94-3.642C3.734 15.026 3 13.574 3 12c0-4.418 3.582-8 8-8s8 3.582 8 8z"/>
                </svg>
                <p class="text-gray-500">Aún no hay reseñas para este anuncio.</p>
                @if ($this->canUserLeaveReview())
                    <p class="text-sm text-gray-400 mt-1">¡Sé el primero en dejar una reseña!</p>
                @endif
            </div>
        @endif
    </div>
</div>