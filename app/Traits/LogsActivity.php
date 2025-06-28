<?php

namespace App\Traits;

use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

trait LogsActivity
{
    /**
     * Log the start of a controller method with request context
     */
    protected function logMethodStart(string $method, Request $request, array $additionalContext = []): string
    {
        $traceId = Str::uuid()->toString();
        
        $context = array_merge([
            'trace_id' => $traceId,
            'controller' => static::class,
            'method' => $method,
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'user_id' => auth()->id(),
            'request_id' => $request->header('X-Request-ID', $traceId),
            'url' => $request->fullUrl(),
            'http_method' => $request->method(),
            'parameters' => $this->sanitizeParameters($request->all()),
            'route_parameters' => $request->route() ? $request->route()->parameters() : [],
            'timestamp' => now()->toISOString(),
        ], $additionalContext);

        Log::channel($this->getLogChannel())->info("[{$this->getLogPrefix()}] Method started: {$method}", $context);
        
        return $traceId;
    }

    /**
     * Log successful completion of a method
     */
    protected function logMethodSuccess(string $traceId, string $method, $result = null, array $additionalContext = []): void
    {
        $context = array_merge([
            'trace_id' => $traceId,
            'controller' => static::class,
            'method' => $method,
            'status' => 'success',
            'result_type' => $result ? gettype($result) : 'null',
            'timestamp' => now()->toISOString(),
        ], $additionalContext);

        // Only log result data for specific types to avoid too much noise
        if (is_array($result) || is_object($result)) {
            $context['result_summary'] = $this->summarizeResult($result);
        }

        Log::channel($this->getLogChannel())->info("[{$this->getLogPrefix()}] Method completed successfully: {$method}", $context);
    }

    /**
     * Log errors with full context
     */
    protected function logMethodError(string $traceId, string $method, \Throwable $exception, array $additionalContext = []): void
    {
        $context = array_merge([
            'trace_id' => $traceId,
            'controller' => static::class,
            'method' => $method,
            'status' => 'error',
            'error_message' => $exception->getMessage(),
            'error_code' => $exception->getCode(),
            'error_file' => $exception->getFile(),
            'error_line' => $exception->getLine(),
            'error_class' => get_class($exception),
            'stack_trace' => $exception->getTraceAsString(),
            'timestamp' => now()->toISOString(),
        ], $additionalContext);

        Log::channel('errors')->error("[{$this->getLogPrefix()}] Method failed: {$method}", $context);
    }

    /**
     * Log warning events
     */
    protected function logWarning(string $traceId, string $method, string $message, array $additionalContext = []): void
    {
        $context = array_merge([
            'trace_id' => $traceId,
            'controller' => static::class,
            'method' => $method,
            'status' => 'warning',
            'message' => $message,
            'timestamp' => now()->toISOString(),
        ], $additionalContext);

        Log::channel($this->getLogChannel())->warning("[{$this->getLogPrefix()}] {$message}", $context);
    }

    /**
     * Log security-related events
     */
    protected function logSecurityEvent(string $traceId, string $event, Request $request, array $additionalContext = []): void
    {
        $context = array_merge([
            'trace_id' => $traceId,
            'controller' => static::class,
            'event_type' => 'security',
            'event' => $event,
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'user_id' => auth()->id(),
            'user_email' => auth()->user()?->email,
            'session_id' => session()->getId(),
            'timestamp' => now()->toISOString(),
        ], $additionalContext);

        Log::channel('security')->warning("[SECURITY][{$this->getLogPrefix()}] {$event}", $context);
    }

    /**
     * Log payment-related events
     */
    protected function logPaymentEvent(string $traceId, string $event, array $paymentData = [], array $additionalContext = []): void
    {
        $sanitizedPaymentData = $this->sanitizePaymentData($paymentData);
        
        $context = array_merge([
            'trace_id' => $traceId,
            'controller' => static::class,
            'event_type' => 'payment',
            'event' => $event,
            'payment_data' => $sanitizedPaymentData,
            'user_id' => auth()->id(),
            'timestamp' => now()->toISOString(),
        ], $additionalContext);

        Log::channel('payment')->info("[PAYMENT][{$this->getLogPrefix()}] {$event}", $context);
    }

    /**
     * Log API request/response cycles
     */
    protected function logApiRequest(string $traceId, Request $request, $response = null, array $additionalContext = []): void
    {
        $context = array_merge([
            'trace_id' => $traceId,
            'controller' => static::class,
            'event_type' => 'api',
            'endpoint' => $request->path(),
            'method' => $request->method(),
            'headers' => $this->sanitizeHeaders($request->headers->all()),
            'parameters' => $this->sanitizeParameters($request->all()),
            'response_status' => $response ? $response->getStatusCode() : null,
            'response_size' => $response ? strlen($response->getContent()) : null,
            'user_id' => auth()->id(),
            'timestamp' => now()->toISOString(),
        ], $additionalContext);

        Log::channel('api')->info("[API][{$this->getLogPrefix()}] Request processed", $context);
    }

    /**
     * Get log prefix based on controller name
     */
    private function getLogPrefix(): string
    {
        $className = class_basename(static::class);
        return str_replace('Controller', '', $className);
    }

    /**
     * Get appropriate log channel based on controller type
     */
    private function getLogChannel(): string
    {
        $className = static::class;
        
        // API controllers
        if (str_contains($className, 'Api\\')) {
            return 'api';
        }
        
        // Auth controllers
        if (str_contains($className, 'Auth\\')) {
            return 'security';
        }
        
        // Payment/Callback controllers
        if (str_contains($className, 'Callback\\') || 
            str_contains($className, 'Payment') || 
            str_contains($className, 'Stripe') ||
            str_contains($className, 'Paypal') ||
            str_contains($className, 'Reservation\\PaymentCallback')) {
            return 'payment';
        }
        
        // File upload controllers
        if (str_contains($className, 'FileUpload')) {
            return 'file_upload';
        }
        
        // Default to daily log
        return 'daily';
    }

    /**
     * Sanitize request parameters to remove sensitive data
     */
    private function sanitizeParameters(array $parameters): array
    {
        $sensitiveKeys = [
            'password', 'password_confirmation', 'token', 'secret', 'api_key',
            'credit_card', 'card_number', 'cvv', 'ssn', 'social_security'
        ];

        foreach ($sensitiveKeys as $key) {
            if (isset($parameters[$key])) {
                $parameters[$key] = '[REDACTED]';
            }
        }

        return $parameters;
    }

    /**
     * Sanitize payment data to remove sensitive information
     */
    private function sanitizePaymentData(array $paymentData): array
    {
        $sensitiveKeys = [
            'card_number', 'cvv', 'card_cvc', 'stripe_token', 'payment_method_id',
            'bank_account', 'routing_number', 'account_number'
        ];

        $sanitized = $paymentData;
        
        foreach ($sensitiveKeys as $key) {
            if (isset($sanitized[$key])) {
                if (is_string($sanitized[$key]) && strlen($sanitized[$key]) > 4) {
                    // Keep last 4 characters for reference
                    $sanitized[$key] = '****' . substr($sanitized[$key], -4);
                } else {
                    $sanitized[$key] = '[REDACTED]';
                }
            }
        }

        return $sanitized;
    }

    /**
     * Sanitize headers to remove sensitive authorization data
     */
    private function sanitizeHeaders(array $headers): array
    {
        $sensitiveHeaders = ['authorization', 'cookie', 'x-api-key'];
        
        foreach ($sensitiveHeaders as $header) {
            if (isset($headers[$header])) {
                $headers[$header] = ['[REDACTED]'];
            }
        }

        return $headers;
    }

    /**
     * Create a summary of the result to avoid logging too much data
     */
    private function summarizeResult($result): array
    {
        if (is_array($result)) {
            return [
                'type' => 'array',
                'count' => count($result),
                'keys' => array_keys($result)
            ];
        }

        if (is_object($result)) {
            return [
                'type' => 'object',
                'class' => get_class($result),
                'properties' => property_exists($result, 'attributes') ? array_keys($result->attributes ?? []) : []
            ];
        }

        return ['type' => gettype($result), 'value' => $result];
    }

    /**
     * Helper method to wrap method execution with logging
     */
    protected function executeWithLogging(Request $request, string $methodName, callable $callback, array $context = [])
    {
        $traceId = $this->logMethodStart($methodName, $request, $context);
        
        try {
            $result = $callback($traceId);
            $this->logMethodSuccess($traceId, $methodName, $result);
            return $result;
        } catch (\Throwable $e) {
            $this->logMethodError($traceId, $methodName, $e, $context);
            throw $e;
        }
    }
}