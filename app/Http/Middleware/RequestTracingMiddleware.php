<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response as BaseResponse;

class RequestTracingMiddleware
{
    /**
     * Handle an incoming request with comprehensive logging
     */
    public function handle(Request $request, Closure $next): BaseResponse
    {
        $traceId = Str::uuid()->toString();
        $startTime = microtime(true);
        
        // Add trace ID to request for use in controllers
        $request->headers->set('X-Trace-ID', $traceId);
        
        // Log incoming request
        $this->logIncomingRequest($traceId, $request, $startTime);
        
        // Process request
        $response = $next($request);
        
        // Log outgoing response
        $this->logOutgoingResponse($traceId, $request, $response, $startTime);
        
        // Add trace ID to response headers for client debugging
        if ($response instanceof Response) {
            $response->headers->set('X-Trace-ID', $traceId);
        }
        
        return $response;
    }

    /**
     * Log incoming request details
     */
    private function logIncomingRequest(string $traceId, Request $request, float $startTime): void
    {
        $context = [
            'trace_id' => $traceId,
            'event_type' => 'request_incoming',
            'method' => $request->method(),
            'url' => $request->fullUrl(),
            'path' => $request->path(),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'user_id' => auth()->id(),
            'session_id' => session()->getId(),
            'request_size' => strlen($request->getContent()),
            'headers' => $this->sanitizeHeaders($request->headers->all()),
            'query_parameters' => $request->query(),
            'is_ajax' => $request->ajax(),
            'is_json' => $request->wantsJson(),
            'timestamp' => now()->toISOString(),
            'route_name' => $request->route() ? $request->route()->getName() : null,
            'route_action' => $request->route() ? $request->route()->getActionName() : null,
        ];

        // Add body data for non-GET requests (with sanitization)
        if (!$request->isMethod('GET')) {
            $context['body_data'] = $this->sanitizeRequestData($request->all());
        }

        Log::info('[REQUEST] Incoming request', $context);
    }

    /**
     * Log outgoing response details
     */
    private function logOutgoingResponse(string $traceId, Request $request, BaseResponse $response, float $startTime): void
    {
        $duration = (microtime(true) - $startTime) * 1000; // Convert to milliseconds
        
        $context = [
            'trace_id' => $traceId,
            'event_type' => 'request_completed',
            'method' => $request->method(),
            'path' => $request->path(),
            'status_code' => $response->getStatusCode(),
            'response_size' => strlen($response->getContent()),
            'duration_ms' => round($duration, 2),
            'memory_usage_mb' => round(memory_get_peak_usage(true) / 1024 / 1024, 2),
            'user_id' => auth()->id(),
            'timestamp' => now()->toISOString(),
        ];

        // Categorize response by status code
        if ($response->getStatusCode() >= 500) {
            $context['response_category'] = 'server_error';
            Log::error('[RESPONSE] Request completed with server error', $context);
        } elseif ($response->getStatusCode() >= 400) {
            $context['response_category'] = 'client_error';
            Log::warning('[RESPONSE] Request completed with client error', $context);
        } else {
            $context['response_category'] = 'success';
            Log::info('[RESPONSE] Request completed successfully', $context);
        }

        // Log performance warnings for slow requests
        if ($duration > 5000) { // 5 seconds
            Log::warning('[PERFORMANCE] Slow request detected', array_merge($context, [
                'performance_issue' => 'slow_response',
                'threshold_ms' => 5000
            ]));
        }

        // Log high memory usage
        if (memory_get_peak_usage(true) > 128 * 1024 * 1024) { // 128MB
            Log::warning('[PERFORMANCE] High memory usage detected', array_merge($context, [
                'performance_issue' => 'high_memory',
                'threshold_mb' => 128
            ]));
        }
    }

    /**
     * Sanitize request headers to remove sensitive information
     */
    private function sanitizeHeaders(array $headers): array
    {
        $sensitiveHeaders = [
            'authorization', 'cookie', 'x-api-key', 'x-auth-token', 
            'x-csrf-token', 'x-xsrf-token'
        ];
        
        $sanitized = [];
        foreach ($headers as $key => $value) {
            if (in_array(strtolower($key), $sensitiveHeaders)) {
                $sanitized[$key] = ['[REDACTED]'];
            } else {
                $sanitized[$key] = $value;
            }
        }
        
        return $sanitized;
    }

    /**
     * Sanitize request data to remove sensitive information
     */
    private function sanitizeRequestData(array $data): array
    {
        $sensitiveKeys = [
            'password', 'password_confirmation', 'token', 'secret', 'api_key',
            'credit_card', 'card_number', 'cvv', 'ssn', 'social_security',
            'current_password', 'new_password', '_token'
        ];

        $sanitized = [];
        foreach ($data as $key => $value) {
            if (in_array(strtolower($key), $sensitiveKeys)) {
                $sanitized[$key] = '[REDACTED]';
            } elseif (is_array($value)) {
                $sanitized[$key] = $this->sanitizeRequestData($value);
            } else {
                $sanitized[$key] = $value;
            }
        }

        return $sanitized;
    }

    /**
     * Handle exceptions during request processing
     */
    public function terminate(Request $request, BaseResponse $response): void
    {
        // Log any uncaught exceptions or final cleanup
        if ($traceId = $request->header('X-Trace-ID')) {
            Log::info('[REQUEST] Request lifecycle completed', [
                'trace_id' => $traceId,
                'final_status' => $response->getStatusCode(),
                'timestamp' => now()->toISOString()
            ]);
        }
    }
}