<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\CustomerReview;
use App\Models\ReviewResponse;
use App\Models\Ad;
use Illuminate\Support\Facades\Auth;

class ReviewsSection extends Component
{
    public Ad $ad;
    public $showReviewForm = false;
    public $showResponseForm = [];
    public $newReview = [
        'rating' => 5,
        'feedback' => ''
    ];
    public $newResponse = [];

    protected $rules = [
        'newReview.rating' => 'required|integer|min:1|max:5',
        'newReview.feedback' => 'required|string|min:10|max:1000',
    ];

    protected $messages = [
        'newReview.rating.required' => 'La calificación es obligatoria.',
        'newReview.rating.min' => 'La calificación debe ser de al menos 1 estrella.',
        'newReview.rating.max' => 'La calificación no puede ser mayor a 5 estrellas.',
        'newReview.feedback.required' => 'El comentario es obligatorio.',
        'newReview.feedback.min' => 'El comentario debe tener al menos 10 caracteres.',
        'newReview.feedback.max' => 'El comentario no puede exceder 1000 caracteres.',
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
            $this->newReview = ['rating' => 5, 'feedback' => ''];
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
            ];
        }
    }

    public function submitReview()
    {
        $this->validate();

        if (!Auth::check()) {
            session()->flash('error', 'Debes estar autenticado para dejar una reseña.');
            return;
        }

        if (Auth::id() === $this->ad->user_id) {
            session()->flash('error', 'No puedes dejar una reseña en tu propio anuncio.');
            return;
        }

        $existingReview = CustomerReview::where('reviewable_id', $this->ad->id)
            ->where('reviewable_type', Ad::class)
            ->where('user_id', Auth::id())
            ->first();

        if ($existingReview) {
            session()->flash('error', 'Ya has dejado una reseña para este anuncio.');
            return;
        }

        try {
            CustomerReview::create([
                'reviewable_id' => $this->ad->id,
                'reviewable_type' => Ad::class,
                'user_id' => Auth::id(),
                'rating' => $this->newReview['rating'],
                'feedback' => $this->newReview['feedback'],
                'moderation_status' => 'pending' // Always pending for moderation
            ]);

            $this->reset('newReview', 'showReviewForm');
            $this->newReview = ['rating' => 5, 'feedback' => ''];
            
            session()->flash('success', '¡Reseña enviada exitosamente y está pendiente de moderación!');
            
            $this->dispatch('review-submitted');
            
        } catch (\Exception $e) {
            session()->flash('error', 'Error al enviar la reseña. Por favor, inténtalo de nuevo.');
        }
    }

    public function submitResponse($reviewId)
    {
        $this->validate([
            "newResponse.$reviewId.response_text" => 'required|string|min:10|max:1000',
        ], [
            "newResponse.$reviewId.response_text.required" => 'La respuesta es obligatoria.',
            "newResponse.$reviewId.response_text.min" => 'La respuesta debe tener al menos 10 caracteres.',
            "newResponse.$reviewId.response_text.max" => 'La respuesta no puede exceder 1000 caracteres.',
        ]);

        $review = CustomerReview::findOrFail($reviewId);

        if (!Auth::check()) {
            session()->flash('error', 'Debes estar autenticado para responder a una reseña.');
            return;
        }

        // Check if the authenticated user is the owner of the ad being reviewed
        if (Auth::id() !== $this->ad->user_id) {
            session()->flash('error', 'Solo el propietario del anuncio puede responder a las reseñas.');
            return;
        }

        try {
            $review->responses()->create([
                'user_id' => Auth::id(),
                'response' => $this->newResponse[$reviewId]['response_text'],
                'moderation_status' => 'approved' // Owner responses are automatically approved
            ]);

            unset($this->newResponse[$reviewId]);
            $this->showResponseForm[$reviewId] = false;
            
            session()->flash('success', '¡Respuesta enviada exitosamente!');
            
            $this->dispatch('response-submitted');
            
        } catch (\Exception $e) {
            session()->flash('error', 'Error al enviar la respuesta. Por favor, inténtalo de nuevo.');
        }
    }

    public function canUserLeaveReview()
    {
        if (!Auth::check()) {
            return false;
        }

        if (Auth::id() === $this->ad->user_id) {
            return false;
        }

        return !CustomerReview::where('reviewable_id', $this->ad->id)
            ->where('reviewable_type', Ad::class)
            ->where('user_id', Auth::id())
            ->exists();
    }

    public function render()
    {
        $reviews = CustomerReview::with(['user', 'responses.user'])
            ->where('reviewable_id', $this->ad->id)
            ->where('reviewable_type', Ad::class)
            ->approved()
            ->latest()
            ->get();

        $averageRating = $reviews->avg('rating');
        $totalReviews = $reviews->count();

        foreach ($reviews as $review) {
            if (!isset($this->showResponseForm[$review->id])) {
                $this->showResponseForm[$review->id] = false;
            }
        }

        return view('livewire.reviews-section', compact('reviews', 'averageRating', 'totalReviews'));
    }
}