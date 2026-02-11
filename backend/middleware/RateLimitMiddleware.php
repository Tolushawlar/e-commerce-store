<?php

namespace App\Middleware;

use App\Middleware\RateLimiter;

/**
 * Rate Limit Middleware
 * Apply rate limiting to API requests
 */
class RateLimitMiddleware
{
    private RateLimiter $rateLimiter;
    private array $config;
    private array $endpointRules = [];

    public function __construct(array $config = [])
    {
        $this->config = $config;
        $this->rateLimiter = new RateLimiter($config);
        
        // Load endpoint-specific rules
        if (isset($config['endpoints']) && is_array($config['endpoints'])) {
            $this->endpointRules = $config['endpoints'];
        }
    }

    /**
     * Handle incoming request
     * 
     * @return void
     */
    public function handle(): void
    {
        // Skip if rate limiting is disabled
        if (isset($this->config['enabled']) && !$this->config['enabled']) {
            return;
        }

        $identifier = $this->getIdentifier();
        $endpoint = $this->getCurrentEndpoint();
        
        // Get endpoint-specific limits or use defaults
        $rules = $this->getEndpointRules($endpoint);
        $limit = $rules['limit'] ?? null;
        $window = $rules['window'] ?? null;

        // Check rate limit
        $result = $this->rateLimiter->attempt($identifier, $limit, $window);

        // Set rate limit headers
        $this->setHeaders($result);

        // Block if not allowed
        if (!$result['allowed']) {
            $this->handleRateLimitExceeded($result);
        }
    }

    /**
     * Get unique identifier for rate limiting
     * Prioritizes: User ID > API Key > IP Address
     * 
     * @return string
     */
    private function getIdentifier(): string
    {
        // Check for authenticated user
        if (isset($_SESSION['user_id'])) {
            return 'user:' . $_SESSION['user_id'];
        }

        // Check for API key
        $apiKey = $this->getApiKey();
        if ($apiKey) {
            return 'api_key:' . $apiKey;
        }

        // Fall back to IP address
        return 'ip:' . $this->getClientIp();
    }

    /**
     * Get client IP address
     * 
     * @return string
     */
    private function getClientIp(): string
    {
        $headers = [
            'HTTP_CF_CONNECTING_IP', // Cloudflare
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_REAL_IP',
            'REMOTE_ADDR'
        ];

        foreach ($headers as $header) {
            if (!empty($_SERVER[$header])) {
                $ip = $_SERVER[$header];
                
                // Handle comma-separated IPs (X-Forwarded-For)
                if (strpos($ip, ',') !== false) {
                    $ips = explode(',', $ip);
                    $ip = trim($ips[0]);
                }
                
                if (filter_var($ip, FILTER_VALIDATE_IP)) {
                    return $ip;
                }
            }
        }

        return '0.0.0.0';
    }

    /**
     * Get API key from request
     * 
     * @return string|null
     */
    private function getApiKey(): ?string
    {
        // Check Authorization header
        if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
            $auth = $_SERVER['HTTP_AUTHORIZATION'];
            
            if (preg_match('/Bearer\s+(.*)$/i', $auth, $matches)) {
                return $matches[1];
            }
        }

        // Check X-API-Key header
        if (isset($_SERVER['HTTP_X_API_KEY'])) {
            return $_SERVER['HTTP_X_API_KEY'];
        }

        return null;
    }

    /**
     * Get current endpoint/route
     * 
     * @return string
     */
    private function getCurrentEndpoint(): string
    {
        $uri = $_SERVER['REQUEST_URI'] ?? '/';
        
        // Remove query string
        $uri = strtok($uri, '?');
        
        return $uri;
    }

    /**
     * Get rate limit rules for endpoint
     * 
     * @param string $endpoint
     * @return array
     */
    private function getEndpointRules(string $endpoint): array
    {
        // Exact match
        if (isset($this->endpointRules[$endpoint])) {
            return $this->endpointRules[$endpoint];
        }

        // Pattern matching (e.g., /api/*)
        foreach ($this->endpointRules as $pattern => $rules) {
            if ($this->matchesPattern($endpoint, $pattern)) {
                return $rules;
            }
        }

        return [];
    }

    /**
     * Check if endpoint matches pattern
     * 
     * @param string $endpoint
     * @param string $pattern
     * @return bool
     */
    private function matchesPattern(string $endpoint, string $pattern): bool
    {
        // Convert wildcard pattern to regex
        $regex = str_replace(
            ['*', '/'],
            ['.*', '\/'],
            $pattern
        );
        
        return preg_match('/^' . $regex . '$/', $endpoint) === 1;
    }

    /**
     * Set rate limit headers
     * 
     * @param array $result
     * @return void
     */
    private function setHeaders(array $result): void
    {
        header('X-RateLimit-Limit: ' . $result['limit']);
        header('X-RateLimit-Remaining: ' . $result['remaining']);
        header('X-RateLimit-Reset: ' . $result['reset']);
    }

    /**
     * Handle rate limit exceeded
     * 
     * @param array $result
     * @return void
     */
    private function handleRateLimitExceeded(array $result): void
    {
        http_response_code(429);
        header('Retry-After: ' . $result['reset']);
        header('Content-Type: application/json');

        $response = [
            'error' => 'Too Many Requests',
            'message' => 'Rate limit exceeded. Please try again later.',
            'limit' => $result['limit'],
            'remaining' => $result['remaining'],
            'reset' => $result['reset'],
            'retry_after' => $result['reset']
        ];

        echo json_encode($response, JSON_PRETTY_PRINT);
        exit;
    }

    /**
     * Get rate limiter instance
     * 
     * @return RateLimiter
     */
    public function getRateLimiter(): RateLimiter
    {
        return $this->rateLimiter;
    }
}
