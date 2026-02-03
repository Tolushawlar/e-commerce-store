<?php

namespace App\Services;

/**
 * Paystack Payment Service
 * Handles Paystack API integration for multi-tenant stores
 */
class PaystackService
{
    private string $secretKey;
    private string $publicKey;
    private string $baseUrl = 'https://api.paystack.co';

    /**
     * Initialize with store's Paystack keys
     */
    public function __construct(?string $secretKey = null, ?string $publicKey = null)
    {
        $this->secretKey = $secretKey ?? '';
        $this->publicKey = $publicKey ?? '';
    }

    /**
     * Set keys from store data
     */
    public function setKeys(array $store): self
    {
        $this->secretKey = $store['paystack_secret_key'] ?? '';
        $this->publicKey = $store['paystack_public_key'] ?? '';
        return $this;
    }

    /**
     * Check if Paystack is configured for this store
     */
    public function isConfigured(): bool
    {
        return !empty($this->secretKey) && !empty($this->publicKey);
    }

    /**
     * Get public key (safe to expose to frontend)
     */
    public function getPublicKey(): string
    {
        return $this->publicKey;
    }

    /**
     * Initialize payment transaction
     * 
     * @param array $data Payment data
     * @return array Response with authorization_url and reference
     */
    public function initializePayment(array $data): array
    {
        if (!$this->isConfigured()) {
            throw new \Exception('Paystack not configured for this store');
        }

        $url = "{$this->baseUrl}/transaction/initialize";

        // Prepare payment data
        $payload = [
            'email' => $data['email'],
            'amount' => $data['amount'] * 100, // Convert to kobo (smallest currency unit)
            'currency' => $data['currency'] ?? 'NGN',
            'reference' => $data['reference'] ?? $this->generateReference(),
            'callback_url' => $data['callback_url'] ?? null,
            'metadata' => $data['metadata'] ?? []
        ];

        // Add optional fields
        if (isset($data['channels'])) {
            $payload['channels'] = $data['channels'];
        }

        $response = $this->makeRequest('POST', $url, $payload);

        if (!$response['status']) {
            throw new \Exception($response['message'] ?? 'Payment initialization failed');
        }

        return $response['data'];
    }

    /**
     * Verify payment transaction
     * 
     * @param string $reference Payment reference
     * @return array Transaction data
     */
    public function verifyPayment(string $reference): array
    {
        if (!$this->isConfigured()) {
            throw new \Exception('Paystack not configured for this store');
        }

        $url = "{$this->baseUrl}/transaction/verify/{$reference}";

        $response = $this->makeRequest('GET', $url);

        if (!$response['status']) {
            throw new \Exception($response['message'] ?? 'Payment verification failed');
        }

        return $response['data'];
    }

    /**
     * Verify webhook signature
     * 
     * @param string $payload Raw POST body
     * @param string $signature X-Paystack-Signature header
     * @return bool
     */
    public function verifyWebhookSignature(string $payload, string $signature): bool
    {
        if (!$this->isConfigured()) {
            return false;
        }

        $computedSignature = hash_hmac('sha512', $payload, $this->secretKey);
        return hash_equals($computedSignature, $signature);
    }

    /**
     * Generate unique payment reference
     * 
     * @return string
     */
    public function generateReference(): string
    {
        return 'PS_' . time() . '_' . bin2hex(random_bytes(8));
    }

    /**
     * Make HTTP request to Paystack API
     * 
     * @param string $method HTTP method
     * @param string $url API endpoint
     * @param array|null $data Request payload
     * @return array Response data
     */
    private function makeRequest(string $method, string $url, ?array $data = null): array
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $this->secretKey,
            'Content-Type: application/json',
            'Cache-Control: no-cache',
        ]);

        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            if ($data) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            }
        }

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);

        curl_close($ch);

        if ($error) {
            throw new \Exception("cURL Error: {$error}");
        }

        $result = json_decode($response, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception('Invalid JSON response from Paystack');
        }

        return $result;
    }

    /**
     * Get transaction details
     * 
     * @param int $transactionId Paystack transaction ID
     * @return array Transaction data
     */
    public function getTransaction(int $transactionId): array
    {
        if (!$this->isConfigured()) {
            throw new \Exception('Paystack not configured for this store');
        }

        $url = "{$this->baseUrl}/transaction/{$transactionId}";
        $response = $this->makeRequest('GET', $url);

        if (!$response['status']) {
            throw new \Exception($response['message'] ?? 'Failed to fetch transaction');
        }

        return $response['data'];
    }

    /**
     * List transactions
     * 
     * @param array $params Query parameters
     * @return array Transactions list
     */
    public function listTransactions(array $params = []): array
    {
        if (!$this->isConfigured()) {
            throw new \Exception('Paystack not configured for this store');
        }

        $queryString = http_build_query($params);
        $url = "{$this->baseUrl}/transaction?" . $queryString;

        $response = $this->makeRequest('GET', $url);

        if (!$response['status']) {
            throw new \Exception($response['message'] ?? 'Failed to fetch transactions');
        }

        return $response['data'];
    }

    /**
     * Format amount from kobo to naira
     * 
     * @param int $amountInKobo Amount in kobo
     * @return float Amount in naira
     */
    public static function formatAmount(int $amountInKobo): float
    {
        return $amountInKobo / 100;
    }

    /**
     * Convert amount to kobo
     * 
     * @param float $amountInNaira Amount in naira
     * @return int Amount in kobo
     */
    public static function toKobo(float $amountInNaira): int
    {
        return (int) ($amountInNaira * 100);
    }
}
