<?php

namespace App\Services;

use App\Models\CustomerReview;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ContentModerationService
{
    protected array $spamKeywords = [
        'comprar', 'vender', 'barato', 'gratis', 'oferta especial', 'descuento',
        'click aquí', 'visita mi página', 'contactame', 'whatsapp', 'telegram'
    ];

    protected array $inappropriateKeywords = [
        // Palabras ofensivas en español (ejemplo reducido)
        'estúpido', 'idiota', 'basura', 'horrible', 'pésimo', 'fraude', 'estafa'
    ];

    protected array $positiveKeywords = [
        'excelente', 'bueno', 'recomendado', 'calidad', 'servicio', 'rápido',
        'profesional', 'confiable', 'satisfecho', 'perfecto', 'increíble'
    ];

    /**
     * Analizar el contenido de una reseña
     */
    public function analyzeContent(CustomerReview $review): array
    {
        $content = $this->prepareContent($review);
        
        $analysis = [
            'score' => 5.0, // Score base
            'flags' => [],
            'confidence' => 0.0,
            'details' => []
        ];

        // Ejecutar diferentes tipos de análisis
        $analysis = $this->analyzeLength($content, $analysis);
        $analysis = $this->analyzeSpam($content, $analysis);
        $analysis = $this->analyzeInappropriateContent($content, $analysis);
        $analysis = $this->analyzeQuality($content, $analysis);
        $analysis = $this->analyzeRatingConsistency($review, $analysis);
        $analysis = $this->analyzeUserHistory($review, $analysis);

        // Calcular confianza final
        $analysis['confidence'] = $this->calculateConfidence($analysis);

        // Normalizar score (0-10)
        $analysis['score'] = max(0, min(10, $analysis['score']));

        return $analysis;
    }

    /**
     * Preparar contenido para análisis
     */
    private function prepareContent(CustomerReview $review): string
    {
        return strtolower(trim($review->feedback));
    }

    /**
     * Analizar longitud del contenido
     */
    private function analyzeLength(string $content, array $analysis): array
    {
        $length = strlen($content);
        
        if ($length < 10) {
            $analysis['score'] -= 2.0;
            $analysis['flags'][] = 'too_short';
            $analysis['details']['length'] = 'Contenido muy corto';
        } elseif ($length > 1000) {
            $analysis['score'] -= 1.0;
            $analysis['flags'][] = 'too_long';
            $analysis['details']['length'] = 'Contenido excesivamente largo';
        } else {
            $analysis['score'] += 0.5;
            $analysis['details']['length'] = 'Longitud apropiada';
        }

        return $analysis;
    }

    /**
     * Detectar spam
     */
    private function analyzeSpam(string $content, array $analysis): array
    {
        $spamCount = 0;
        $detectedSpam = [];

        foreach ($this->spamKeywords as $keyword) {
            if (Str::contains($content, $keyword)) {
                $spamCount++;
                $detectedSpam[] = $keyword;
            }
        }

        if ($spamCount >= 3) {
            $analysis['score'] -= 4.0;
            $analysis['flags'][] = 'spam_detected';
            $analysis['details']['spam'] = 'Alto contenido de spam detectado: ' . implode(', ', $detectedSpam);
        } elseif ($spamCount >= 1) {
            $analysis['score'] -= 1.5;
            $analysis['flags'][] = 'potential_spam';
            $analysis['details']['spam'] = 'Posible contenido de spam: ' . implode(', ', $detectedSpam);
        }

        // Detectar patrones de spam
        if ($this->hasSpamPatterns($content)) {
            $analysis['score'] -= 2.0;
            $analysis['flags'][] = 'spam_patterns';
            $analysis['details']['patterns'] = 'Patrones de spam detectados';
        }

        return $analysis;
    }

    /**
     * Detectar contenido inapropiado
     */
    private function analyzeInappropriateContent(string $content, array $analysis): array
    {
        $inappropriateCount = 0;
        $detectedWords = [];

        foreach ($this->inappropriateKeywords as $keyword) {
            if (Str::contains($content, $keyword)) {
                $inappropriateCount++;
                $detectedWords[] = $keyword;
            }
        }

        if ($inappropriateCount >= 2) {
            $analysis['score'] -= 5.0;
            $analysis['flags'][] = 'offensive_language';
            $analysis['details']['inappropriate'] = 'Lenguaje ofensivo detectado';
        } elseif ($inappropriateCount >= 1) {
            $analysis['score'] -= 2.0;
            $analysis['flags'][] = 'mild_inappropriate';
            $analysis['details']['inappropriate'] = 'Contenido potencialmente inapropiado';
        }

        return $analysis;
    }

    /**
     * Analizar calidad del contenido
     */
    private function analyzeQuality(string $content, array $analysis): array
    {
        $positiveCount = 0;
        $sentences = explode('.', $content);
        $wordCount = str_word_count($content);

        // Contar palabras positivas
        foreach ($this->positiveKeywords as $keyword) {
            if (Str::contains($content, $keyword)) {
                $positiveCount++;
            }
        }

        // Evaluar estructura
        if (count($sentences) > 1 && $wordCount > 20) {
            $analysis['score'] += 1.0;
            $analysis['details']['structure'] = 'Buena estructura de contenido';
        }

        // Evaluar positividad
        if ($positiveCount >= 2) {
            $analysis['score'] += 1.5;
            $analysis['details']['tone'] = 'Tono positivo y constructivo';
        }

        // Detectar contenido repetitivo
        if ($this->isRepetitive($content)) {
            $analysis['score'] -= 1.5;
            $analysis['flags'][] = 'repetitive_content';
            $analysis['details']['repetitive'] = 'Contenido repetitivo detectado';
        }

        return $analysis;
    }

    /**
     * Analizar consistencia entre rating y contenido
     */
    private function analyzeRatingConsistency(CustomerReview $review, array $analysis): array
    {
        $content = $this->prepareContent($review);
        $rating = $review->rating;
        
        $positiveWords = 0;
        $negativeWords = 0;

        // Palabras positivas vs negativas
        foreach ($this->positiveKeywords as $keyword) {
            if (Str::contains($content, $keyword)) {
                $positiveWords++;
            }
        }

        $negativeKeywords = ['malo', 'terrible', 'horrible', 'pésimo', 'no recomiendo'];
        foreach ($negativeKeywords as $keyword) {
            if (Str::contains($content, $keyword)) {
                $negativeWords++;
            }
        }

        // Verificar consistencia
        if ($rating >= 4 && $negativeWords > $positiveWords) {
            $analysis['score'] -= 2.0;
            $analysis['flags'][] = 'rating_inconsistency';
            $analysis['details']['consistency'] = 'Rating alto con contenido negativo';
        } elseif ($rating <= 2 && $positiveWords > $negativeWords) {
            $analysis['score'] -= 2.0;
            $analysis['flags'][] = 'rating_inconsistency';
            $analysis['details']['consistency'] = 'Rating bajo con contenido positivo';
        } else {
            $analysis['score'] += 0.5;
            $analysis['details']['consistency'] = 'Rating consistente con el contenido';
        }

        return $analysis;
    }

    /**
     * Analizar historial del usuario
     */
    private function analyzeUserHistory(CustomerReview $review, array $analysis): array
    {
        $user = $review->user;
        $userReviews = CustomerReview::where('user_id', $user->id)
                                   ->where('id', '!=', $review->id)
                                   ->get();

        if ($userReviews->count() === 0) {
            $analysis['details']['user_history'] = 'Usuario nuevo, primera reseña';
            return $analysis;
        }

        // Verificar patrones sospechosos
        $recentReviews = $userReviews->where('created_at', '>=', now()->subDay())->count();
        if ($recentReviews >= 5) {
            $analysis['score'] -= 3.0;
            $analysis['flags'][] = 'suspicious_activity';
            $analysis['details']['user_history'] = 'Actividad sospechosa: muchas reseñas en poco tiempo';
        }

        // Verificar si siempre da la misma calificación
        $ratings = $userReviews->pluck('rating')->unique();
        if ($ratings->count() === 1 && $userReviews->count() >= 3) {
            $analysis['score'] -= 1.5;
            $analysis['flags'][] = 'monotone_rating';
            $analysis['details']['user_history'] = 'Usuario siempre da la misma calificación';
        }

        return $analysis;
    }

    /**
     * Detectar patrones de spam
     */
    private function hasSpamPatterns(string $content): bool
    {
        // Exceso de signos de exclamación
        if (substr_count($content, '!') > 3) {
            return true;
        }

        // Exceso de mayúsculas
        if (strlen($content) > 20 && strlen(preg_replace('/[^A-Z]/', '', $content)) / strlen($content) > 0.5) {
            return true;
        }

        // URLs o menciones de contacto
        if (preg_match('/https?:\/\/|www\.|\.com|@|\+\d{2,3}/', $content)) {
            return true;
        }

        return false;
    }

    /**
     * Detectar contenido repetitivo
     */
    private function isRepetitive(string $content): bool
    {
        $words = explode(' ', $content);
        $wordCount = array_count_values($words);
        
        foreach ($wordCount as $count) {
            if ($count > 3 && strlen($content) < 100) {
                return true;
            }
        }

        return false;
    }

    /**
     * Calcular confianza del análisis
     */
    private function calculateConfidence(array $analysis): float
    {
        $confidence = 0.7; // Base

        // Incrementar confianza basada en evidencia
        if (!empty($analysis['flags'])) {
            $confidence += 0.1 * count($analysis['flags']);
        }

        if (isset($analysis['details']['consistency'])) {
            $confidence += 0.1;
        }

        if (isset($analysis['details']['user_history'])) {
            $confidence += 0.05;
        }

        return min(1.0, $confidence);
    }

    /**
     * Integración con servicios externos (opcional)
     */
    public function analyzeWithExternalService(string $content): array
    {
        try {
            // Ejemplo de integración con Google Perspective API
            $response = Http::timeout(10)->post('https://commentanalyzer.googleapis.com/v1alpha1/comments:analyze', [
                'key' => config('services.perspective.api_key'),
                'requestedAttributes' => [
                    'TOXICITY' => [],
                    'SPAM' => [],
                    'IDENTITY_ATTACK' => []
                ],
                'comment' => ['text' => $content]
            ]);

            if ($response->successful()) {
                return $response->json();
            }

        } catch (\Exception $e) {
            Log::warning('Error al conectar con servicio externo de moderación', [
                'error' => $e->getMessage()
            ]);
        }

        return [];
    }
}