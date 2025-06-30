<?php

namespace App\Livewire\Review;

use App\Models\CustomerReview;
use App\Models\ReviewResponse;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\Attributes\On;

class ReviewResponseManager extends Component
{
    // Props del componente
    public CustomerReview $review;
    public bool $showResponseForm = false;
    public bool $canRespond = false;
    
    // Formulario de respuesta
    public string $responseContent = '';
    public bool $isSubmitting = false;
    
    // Estado de edición
    public ?ReviewResponse $editingResponse = null;
    public string $editContent = '';
    public bool $isEditing = false;
    
    // Interacciones
    public array $userInteractions = [];

    protected $rules = [
        'responseContent' => 'required|string|min:10|max:1000',
        'editContent' => 'required|string|min:10|max:1000'
    ];

    protected $messages = [
        'responseContent.required' => 'La respuesta es obligatoria.',
        'responseContent.min' => 'La respuesta debe tener al menos 10 caracteres.',
        'responseContent.max' => 'La respuesta no puede exceder 1000 caracteres.',
        'editContent.required' => 'El contenido editado es obligatorio.',
        'editContent.min' => 'El contenido debe tener al menos 10 caracteres.',
        'editContent.max' => 'El contenido no puede exceder 1000 caracteres.'
    ];

    public function mount(CustomerReview $review): void
    {
        $this->review = $review;
        $this->checkUserPermissions();
        $this->loadUserInteractions();
    }

    public function render()
    {
        $responses = $this->review->visibleResponses()->with(['user', 'interactions'])->get();
        
        return view('livewire.review.review-response-manager', [
            'responses' => $responses,
            'primaryResponse' => $this->review->getPrimaryResponse(),
            'responseStats' => $this->review->response_stats
        ]);
    }

    // ==================== GESTIÓN DE PERMISOS ====================

    private function checkUserPermissions(): void
    {
        $user = Auth::user();
        $this->canRespond = $user && $this->review->canUserRespond($user);
    }

    private function loadUserInteractions(): void
    {
        $user = Auth::user();
        if (!$user) {
            return;
        }

        $this->userInteractions = $this->review->responses()
            ->with(['interactions' => function ($query) use ($user) {
                $query->where('user_id', $user->id);
            }])
            ->get()
            ->mapWithKeys(function ($response) {
                return [$response->id => $response->interactions->keyBy('interaction_type')];
            })
            ->toArray();
    }

    // ==================== ACCIONES DE RESPUESTA ====================

    public function toggleResponseForm(): void
    {
        if (!$this->canRespond) {
            $this->dispatch('error', message: 'No tienes permisos para responder a esta reseña.');
            return;
        }

        $this->showResponseForm = !$this->showResponseForm;
        $this->responseContent = '';
        $this->resetValidation();
    }

    public function submitResponse(): void
    {
        if (!$this->canRespond) {
            $this->dispatch('error', message: 'No tienes permisos para responder.');
            return;
        }

        $this->validate(['responseContent' => $this->rules['responseContent']]);

        $this->isSubmitting = true;

        try {
            $user = Auth::user();
            $response = $this->review->createResponse($user, $this->responseContent);

            if ($response) {
                $this->dispatch('response-created', message: 'Respuesta publicada exitosamente.');
                $this->resetForm();
                $this->checkUserPermissions(); // Actualizar permisos después de responder
                
                // Enviar notificación al autor de la reseña
                $this->notifyReviewAuthor($response);
            } else {
                $this->dispatch('error', message: 'No se pudo crear la respuesta. Verifica tus permisos.');
            }

        } catch (\Exception $e) {
            $this->dispatch('error', message: 'Error al publicar la respuesta: ' . $e->getMessage());
        } finally {
            $this->isSubmitting = false;
        }
    }

    public function cancelResponse(): void
    {
        $this->showResponseForm = false;
        $this->resetForm();
    }

    private function resetForm(): void
    {
        $this->responseContent = '';
        $this->showResponseForm = false;
        $this->resetValidation();
    }

    // ==================== EDICIÓN DE RESPUESTAS ====================

    public function startEdit(ReviewResponse $response): void
    {
        $user = Auth::user();
        
        if (!$user || !$response->canUserEdit($user)) {
            $this->dispatch('error', message: 'No puedes editar esta respuesta.');
            return;
        }

        $this->editingResponse = $response;
        $this->editContent = $response->response;
        $this->isEditing = true;
        $this->resetValidation();
    }

    public function saveEdit(): void
    {
        if (!$this->editingResponse) {
            return;
        }

        $this->validate(['editContent' => $this->rules['editContent']]);

        try {
            $user = Auth::user();
            $success = $this->editingResponse->editContent($user, $this->editContent);

            if ($success) {
                $this->dispatch('response-updated', message: 'Respuesta actualizada exitosamente.');
                $this->cancelEdit();
            } else {
                $this->dispatch('error', message: 'No se pudo actualizar la respuesta.');
            }

        } catch (\Exception $e) {
            $this->dispatch('error', message: 'Error al actualizar: ' . $e->getMessage());
        }
    }

    public function cancelEdit(): void
    {
        $this->editingResponse = null;
        $this->editContent = '';
        $this->isEditing = false;
        $this->resetValidation();
    }

    // ==================== INTERACCIONES ====================

    public function markResponseAsHelpful(ReviewResponse $response): void
    {
        $user = Auth::user();
        
        if (!$user) {
            $this->dispatch('error', message: 'Debes iniciar sesión para votar.');
            return;
        }

        if (!$response->canUserInteract($user)) {
            $this->dispatch('error', message: 'No puedes interactuar con esta respuesta.');
            return;
        }

        try {
            $success = $response->markAsHelpful($user);
            
            if ($success) {
                $this->dispatch('interaction-success', message: 'Marcado como útil.');
                $this->loadUserInteractions();
            } else {
                $this->dispatch('info', message: 'Ya marcaste esta respuesta como útil.');
            }

        } catch (\Exception $e) {
            $this->dispatch('error', message: 'Error al procesar la interacción.');
        }
    }

    public function markResponseAsNotHelpful(ReviewResponse $response): void
    {
        $user = Auth::user();
        
        if (!$user) {
            $this->dispatch('error', message: 'Debes iniciar sesión para votar.');
            return;
        }

        if (!$response->canUserInteract($user)) {
            $this->dispatch('error', message: 'No puedes interactuar con esta respuesta.');
            return;
        }

        try {
            $success = $response->markAsNotHelpful($user);
            
            if ($success) {
                $this->dispatch('interaction-success', message: 'Marcado como no útil.');
                $this->loadUserInteractions();
            } else {
                $this->dispatch('info', message: 'Ya marcaste esta respuesta como no útil.');
            }

        } catch (\Exception $e) {
            $this->dispatch('error', message: 'Error al procesar la interacción.');
        }
    }

    public function reportResponse(ReviewResponse $response, string $reason, string $description = ''): void
    {
        $user = Auth::user();
        
        if (!$user) {
            $this->dispatch('error', message: 'Debes iniciar sesión para reportar.');
            return;
        }

        if (!$response->canUserInteract($user)) {
            $this->dispatch('error', message: 'No puedes reportar esta respuesta.');
            return;
        }

        try {
            $success = $response->report($user, $reason, $description);
            
            if ($success) {
                $this->dispatch('report-success', message: 'Respuesta reportada. Será revisada por nuestro equipo.');
                $this->loadUserInteractions();
            } else {
                $this->dispatch('info', message: 'Ya reportaste esta respuesta anteriormente.');
            }

        } catch (\Exception $e) {
            $this->dispatch('error', message: 'Error al procesar el reporte.');
        }
    }

    // ==================== ELIMINACIÓN ====================

    public function deleteResponse(ReviewResponse $response): void
    {
        $user = Auth::user();
        
        if (!$user || !$response->canUserDelete($user)) {
            $this->dispatch('error', message: 'No puedes eliminar esta respuesta.');
            return;
        }

        try {
            $response->delete();
            $this->dispatch('response-deleted', message: 'Respuesta eliminada exitosamente.');
            $this->checkUserPermissions(); // Actualizar permisos después de eliminar

        } catch (\Exception $e) {
            $this->dispatch('error', message: 'Error al eliminar la respuesta.');
        }
    }

    // ==================== UTILIDADES ====================

    public function getUserInteraction(string $responseId, string $interactionType): ?array
    {
        return $this->userInteractions[$responseId][$interactionType] ?? null;
    }

    public function hasUserInteracted(string $responseId, string $interactionType): bool
    {
        return !is_null($this->getUserInteraction($responseId, $interactionType));
    }

    private function notifyReviewAuthor(ReviewResponse $response): void
    {
        // Enviar notificación a través de un job para mejor rendimiento
        try {
            \App\Jobs\Review\SendResponseNotificationJob::dispatch($this->review, $response);
            
            \Log::info('Job de notificación de respuesta encolado', [
                'review_id' => $this->review->id,
                'response_id' => $response->id
            ]);
        } catch (\Exception $e) {
            // Log del error pero no interrumpir el flujo principal
            \Log::warning('Error encolando notificación de respuesta', [
                'review_id' => $this->review->id,
                'response_id' => $response->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    // ==================== EVENTOS ====================

    #[On('response-form-opened')]
    public function onResponseFormOpened(): void
    {
        $this->checkUserPermissions();
    }

    #[On('user-logged-in')]
    public function onUserLoggedIn(): void
    {
        $this->checkUserPermissions();
        $this->loadUserInteractions();
    }

    #[On('review-updated')]
    public function onReviewUpdated(): void
    {
        $this->review->refresh();
        $this->checkUserPermissions();
    }
}