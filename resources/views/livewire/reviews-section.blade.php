<div class="space-y-6">
    <!-- Flash Messages -->
    @if (session()->has('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif

    <!-- Reviews Header -->
    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-4">
            <h3 class="text-xl font-semibold dark:text-white">Reseñas ({{ $totalReviews }})</h3>
            @if($totalReviews > 0)
                <div class="flex items-center space-x-2">
                    <div class="flex items-center">
                        @for($i = 1; $i <= 5; $i++)
                            <svg class="w-5 h-5 {{ $i <= round($averageRating) ? 'text-yellow-400' : 'text-gray-300' }}" 
                                 fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                        @endfor
                    </div>
                    <span class="text-lg font-medium dark:text-white">{{ number_format($averageRating, 1) }}</span>
                </div>
            @endif
        </div>
        
        @auth
            @if($this->canUserLeaveReview())
                <button wire:click="toggleReviewForm" 
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-200 flex items-center space-x-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    <span>{{ $showReviewForm ? 'Cancelar' : 'Escribir Reseña' }}</span>
                </button>
            @endif
        @else
            <a href="{{ route('login') }}" 
               class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition duration-200">
                Inicia sesión para reseñar
            </a>
        @endauth
    </div>

    <!-- New Review Form -->
    @if($showReviewForm)
        <div class="bg-gray-50 dark:bg-gray-800 p-6 rounded-lg border">
            <h4 class="text-lg font-medium mb-4 dark:text-white">Escribe tu reseña</h4>
            <form wire:submit.prevent="submitReview">
                <div class="space-y-4">
                    <!-- Rating -->
                    <div>
                        <label class="block text-sm font-medium mb-2 dark:text-gray-300">Calificación</label>
                        <div class="flex items-center space-x-1">
                            @for($i = 1; $i <= 5; $i++)
                                <button type="button" 
                                        wire:click="$set('newReview.rating', {{ $i }})"
                                        class="focus:outline-none transition duration-150">
                                    <svg class="w-8 h-8 {{ $i <= $newReview['rating'] ? 'text-yellow-400' : 'text-gray-300' }} hover:text-yellow-400" 
                                         fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                </button>
                            @endfor
                            <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">{{ $newReview['rating'] }} de 5 estrellas</span>
                        </div>
                        @error('newReview.rating') 
                            <span class="text-red-500 text-sm mt-1">{{ $message }}</span> 
                        @enderror
                    </div>

                    <!-- Feedback -->
                    <div>
                        <label class="block text-sm font-medium mb-2 dark:text-gray-300">Tu experiencia</label>
                        <textarea wire:model="newReview.feedback" 
                                  rows="4" 
                                  class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white resize-none"
                                  placeholder="Comparte tu experiencia con este servicio..."></textarea>
                        <div class="flex justify-between mt-1">
                            @error('newReview.feedback') 
                                <span class="text-red-500 text-sm">{{ $message }}</span> 
                            @else
                                <span></span>
                            @enderror
                            <span class="text-xs text-gray-500">{{ strlen($newReview['feedback'] ?? '') }}/1000</span>
                        </div>
                    </div>

                    <!-- Submit Buttons -->
                    <div class="flex justify-end space-x-3">
                        <button type="button" 
                                wire:click="toggleReviewForm"
                                class="px-4 py-2 text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 transition duration-200">
                            Cancelar
                        </button>
                        <button type="submit" 
                                class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition duration-200 flex items-center space-x-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                            </svg>
                            <span>Enviar Reseña</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    @endif

    <!-- Reviews List -->
    <div class="space-y-6">
        @forelse($reviews as $review)
            <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-6 bg-white dark:bg-gray-800">
                <!-- Review Header -->
                <div class="flex items-start justify-between mb-4">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white font-semibold">
                            {{ strtoupper(substr($review->user->name, 0, 1)) }}
                        </div>
                        <div>
                            <p class="font-medium dark:text-white">{{ $review->user->name }}</p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $review->created_at->format('d M Y') }}</p>
                        </div>
                    </div>
                    
                    <!-- Rating Stars -->
                    <div class="flex items-center space-x-1">
                        @for($i = 1; $i <= 5; $i++)
                            <svg class="w-5 h-5 {{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-300' }}" 
                                 fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                        @endfor
                        <span class="ml-1 text-sm font-medium dark:text-white">{{ $review->rating }}</span>
                    </div>
                </div>

                <!-- Review Feedback -->
                <p class="text-gray-700 dark:text-gray-300 mb-4 leading-relaxed">{{ $review->feedback }}</p>

                <!-- Response Section -->
                @if($review->responses->first())
                    <div class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-lg border-l-4 border-blue-500 mt-4">
                        <div class="flex items-start justify-between mb-2">
                            <div class="flex items-center space-x-2">
                                <div class="w-8 h-8 bg-gradient-to-r from-green-500 to-teal-600 rounded-full flex items-center justify-center text-white text-sm font-semibold">
                                    {{ strtoupper(substr($review->responses->first()->user->name, 0, 1)) }}
                                </div>
                                <div>
                                    <p class="font-medium text-sm dark:text-white">{{ $review->responses->first()->user->name }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                        Respuesta del proveedor • {{ $review->responses->first()->created_at->format('d M Y') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                        <p class="text-gray-700 dark:text-gray-300 text-sm leading-relaxed">{{ $review->responses->first()->response }}</p>
                    </div>
                @else
                    <!-- Response Form (Only for Ad Owner) -->
                    @auth
                        @if(auth()->id() === $ad->user_id && !$review->responses->count())
                            @if(!($showResponseForm[$review->id] ?? false))
                                <button wire:click="toggleResponseForm({{ $review->id }})"
                                        class="mt-3 text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 text-sm font-medium flex items-center space-x-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path>
                                    </svg>
                                    <span>Responder</span>
                                </button>
                            @else
                                <div class="mt-4 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                    <h5 class="font-medium mb-3 dark:text-white">Responder a la reseña</h5>
                                    <div class="space-y-4">
                                        <!-- Response Text -->
                                        <div>
                                            <textarea wire:model="newResponse.{{ $review->id }}.response_text" 
                                                      rows="3" 
                                                      class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-600 dark:text-white resize-none"
                                                      placeholder="Escribe tu respuesta..."></textarea>
                                            @error("newResponse.{$review->id}.response_text") 
                                                <span class="text-red-500 text-sm mt-1">{{ $message }}</span> 
                                            @enderror
                                        </div>

                                        <!-- Submit Buttons -->
                                        <div class="flex justify-end space-x-3">
                                            <button type="button" 
                                                    wire:click="toggleResponseForm({{ $review->id }})"
                                                    class="px-4 py-2 text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 transition duration-200 text-sm">
                                                Cancelar
                                            </button>
                                            <button wire:click="submitResponse({{ $review->id }})"
                                                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-200 text-sm flex items-center space-x-1">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                                                </svg>
                                                <span>Enviar Respuesta</span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endif
                    @endauth
                @endif
            </div>
        @empty
            <div class="text-center py-12 bg-gray-50 dark:bg-gray-800 rounded-lg">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10m0 0V6a2 2 0 00-2-2H9a2 2 0 00-2 2v2m0 0v10a2 2 0 002 2h6a2 2 0 002-2V8m-9 4h4"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">Sin reseñas aún</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">¡Sé el primero en dejar una reseña!</p>
            </div>
        @endforelse
    </div>
</div>