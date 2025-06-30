<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Review;
use App\Models\BidirectionalReviewResponse;
use App\Models\Ad;
use Illuminate\Support\Facades\Auth;

class BidirectionalReviewsSection extends Component
{
    public Ad $ad;
    public $showReviewForm = false;
    public $showResponseForm = [];
    public $newReview = [
        'rating' => 5,
        'comment' => ''
    ];
    public $newResponse = [];
    public $newProviderReview = [
        'rating' => 5,
        'comment' => ''
    ];

    protected $rules = [
        'newReview.rating' => 'required|integer|min:1|max:5',
        'newReview.comment' => 'required|string|min:10|max:1000',
    ];

    protected $messages = [
        'newReview.rating.required' => 'La calificación es obligatoria.',
        'newReview.rating.min' => 'La calificación debe ser de al menos 1 estrella.',
        'newReview.rating.max' => 'La calificación no puede ser mayor a 5 estrellas.',
        'newReview.comment.required' => 'El comentario es obligatorio.',
        'newReview.comment.min' => 'El comentario debe tener al menos 10 caracteres.',
        'newReview.comment.max' => 'El comentario no puede exceder 1000 caracteres.',
    ];

    public function mount(Ad $ad)
    {
        $this->ad = $ad;
    }

    public function toggleReviewForm()
    {
        $this->showReviewForm = !$this->showReviewForm;
        
        if (!$this->showReviewForm) {
            $this->reset('newReview');
            $this->newReview = ['rating' => 5, 'comment' => ''];
            $this->resetValidation();
        }
    }

    public function toggleResponseForm($reviewId)
    {
        $this->showResponseForm[$reviewId] = !($this->showResponseForm[$reviewId] ?? false);
        
        if (!$this->showResponseForm[$reviewId]) {
            unset($this->newResponse[$reviewId]);
        } else {
            $this->newResponse[$reviewId] = [
                'response_text' => '',
                'client_rating' => null
            ];
        }
    }

    public function submitReview()
    {
        $this->validate();

        // Verificar que el usuario esté autenticado
        if (!Auth::check()) {
            session()->flash('error', 'Debes estar autenticado para dejar una reseña.');
            return;
        }

        // Verificar que no sea el propietario del anuncio
        if (Auth::id() === $this->ad->user_id) {
            session()->flash('error', 'No puedes dejar una reseña en tu propio anuncio.');
            return;
        }

        // Verificar que no haya dejado ya una reseña
        $existingReview = Review::where('ad_id', $this->ad->id)
            ->where('reviewer_id', Auth::id())
            ->where('reviewer_type', 'client')
            ->first();

        if ($existingReview) {
            session()->flash('error', 'Ya has dejado una reseña para este anuncio.');
            return;
        }

        try {
            Review::create([
                'ad_id' => $this->ad->id,
                'reviewer_id' => Auth::id(),
                'reviewed_id' => $this->ad->user_id,
                'reviewer_type' => 'client',
                'rating' => $this->newReview['rating'],
                'comment' => $this->newReview['comment'],
                'status' => 'approved' // O 'pending' si requiere moderación
            ]);

            $this->reset('newReview', 'showReviewForm');
            $this->newReview = ['rating' => 5, 'comment' => ''];
            $this->resetValidation();
            
            session()->flash('success', '¡Reseña enviada exitosamente!');
            
            // Emitir evento para actualizar la vista
            $this->dispatch('review-submitted');
            
        } catch (\Exception $e) {
            session()->flash('error', 'Error al enviar la reseña. Por favor, inténtalo de nuevo.');
        }
    }

    public function submitResponse($reviewId)
    {
        // Validar datos de respuesta
        $this->validate([
            "newResponse.$reviewId.response_text" => 'required|string|min:10|max:1000',
            "newResponse.$reviewId.client_rating" => 'nullable|integer|min:1|max:5',
        ], [
            "newResponse.$reviewId.response_text.required" => 'La respuesta es obligatoria.',
            "newResponse.$reviewId.response_text.min" => 'La respuesta debe tener al menos 10 caracteres.',
            "newResponse.$reviewId.response_text.max" => 'La respuesta no puede exceder 1000 caracteres.',
            "newResponse.$reviewId.client_rating.min" => 'La calificación debe ser de al menos 1 estrella.',
            "newResponse.$reviewId.client_rating.max" => 'La calificación no puede ser mayor a 5 estrellas.',
        ]);

        $review = Review::findOrFail($reviewId);

        // Verificar permisos
        if (!$review->canBeRespondedBy(Auth::user())) {
            session()->flash('error', 'No tienes permisos para responder a esta reseña.');
            return;
        }

        try {
            BidirectionalReviewResponse::create([
                'review_id' => $reviewId,
                'responder_id' => Auth::id(),
                'response_text' => $this->newResponse[$reviewId]['response_text'],
                'client_rating' => $this->newResponse[$reviewId]['client_rating'] ?: null,
            ]);

            unset($this->newResponse[$reviewId]);
            $this->showResponseForm[$reviewId] = false;
            $this->resetValidation();
            
            session()->flash('success', '¡Respuesta enviada exitosamente!');
            
            // Emitir evento para actualizar la vista
            $this->dispatch('response-submitted');
            
        } catch (\Exception $e) {
            session()->flash('error', 'Error al enviar la respuesta. Por favor, inténtalo de nuevo.');
        }
    }

    public function getUserInteraction($reviewId)
    {
        if (!Auth::check()) {
            return null;
        }

        return Review::where('ad_id', $this->ad->id)
            ->where('reviewer_id', Auth::id())
            ->where('reviewer_type', 'client')
            ->first();
    }

    public function canUserLeaveReview()
    {
        if (!Auth::check()) {
            return false;
        }

        // No puede dejar reseña en su propio anuncio
        if (Auth::id() === $this->ad->user_id) {
            return false;
        }

        // Verificar que no haya dejado ya una reseña
        return !Review::where('ad_id', $this->ad->id)
            ->where('reviewer_id', Auth::id())
            ->where('reviewer_type', 'client')
            ->exists();
    }

    public function canUserLeaveProviderReview()
    {
        if (!Auth::check()) {
            return false;
        }

        // Solo el propietario del anuncio puede dejar reseñas de proveedor
        if (Auth::id() !== $this->ad->user_id) {
            return false;
        }

        // Verificar que no haya dejado ya una reseña de proveedor
        return !Review::where('ad_id', $this->ad->id)
            ->where('reviewer_id', Auth::id())
            ->where('reviewer_type', 'provider')
            ->exists();
    }

    public function submitProviderReview($clientId)
    {
        $this->validate([
            'newProviderReview.rating' => 'required|integer|min:1|max:5',
            'newProviderReview.comment' => 'required|string|min:10|max:1000',
        ], [
            'newProviderReview.rating.required' => 'La calificación es obligatoria.',
            'newProviderReview.rating.min' => 'La calificación debe ser de al menos 1 estrella.',
            'newProviderReview.rating.max' => 'La calificación no puede ser mayor a 5 estrellas.',
            'newProviderReview.comment.required' => 'El comentario es obligatorio.',
            'newProviderReview.comment.min' => 'El comentario debe tener al menos 10 caracteres.',
            'newProviderReview.comment.max' => 'El comentario no puede exceder 1000 caracteres.',
        ]);

        if (!Auth::check() || Auth::id() !== $this->ad->user_id) {
            session()->flash('error', 'No tienes permisos para dejar esta reseña.');
            return;
        }

        try {
            Review::create([
                'ad_id' => $this->ad->id,
                'reviewer_id' => Auth::id(),
                'reviewed_id' => $clientId,
                'reviewer_type' => 'provider',
                'rating' => $this->newProviderReview['rating'],
                'comment' => $this->newProviderReview['comment'],
                'status' => 'approved'
            ]);

            $this->reset('newProviderReview');
            $this->newProviderReview = ['rating' => 5, 'comment' => ''];
            $this->resetValidation();
            session()->flash('success', '¡Reseña al cliente enviada exitosamente!');
            
        } catch (\Exception $e) {
            session()->flash('error', 'Error al enviar la reseña del cliente.');
        }
    }

    public function render()
    {
        // Reseñas de clientes al proveedor
        $clientReviews = Review::with(['reviewer', 'response.responder'])
            ->where('ad_id', $this->ad->id)
            ->where('reviewer_type', 'client')
            ->approved()
            ->latest()
            ->get();

        // Reseñas del proveedor a clientes
        $providerReviews = Review::with(['reviewer', 'reviewed'])
            ->where('ad_id', $this->ad->id)
            ->where('reviewer_type', 'provider')
            ->approved()
            ->latest()
            ->get();

        $averageRating = $clientReviews->avg('rating') ?? 0;
        $totalReviews = $clientReviews->count();

        // Preparar datos para mostrar respuestas
        foreach ($clientReviews as $review) {
            if (!isset($this->showResponseForm[$review->id])) {
                $this->showResponseForm[$review->id] = false;
            }
        }

        return view('livewire.bidirectional-reviews-section', compact(
            'clientReviews', 
            'providerReviews', 
            'averageRating', 
            'totalReviews'
        ));
    }
}