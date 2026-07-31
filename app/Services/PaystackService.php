<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PaystackService
{
    protected $secretKey;
    protected $publicKey;
    protected $baseUrl;

    public function __construct()
    {
        $this->secretKey = config('services.paystack.secret');
        $this->publicKey = config('services.paystack.public');
        $this->baseUrl = 'https://api.paystack.co';
    }

    /**
     * Initialize a transaction
     */
    public function initializeTransaction($data)
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->secretKey,
            'Content-Type' => 'application/json',
        ])->post($this->baseUrl . '/transaction/initialize', [
            'amount' => $data['amount'] * 100, // Convert to kobo
            'email' => $data['email'],
            'reference' => $data['reference'] ?? $this->generateReference(),
            'callback_url' => $data['callback_url'] ?? route('paystack.callback'),
            'metadata' => $data['metadata'] ?? [],
        ]);

        if ($response->successful()) {
            return [
                'success' => true,
                'data' => $response->json()['data'],
                'authorization_url' => $response->json()['data']['authorization_url'],
                'reference' => $response->json()['data']['reference'],
            ];
        }

        Log::error('Paystack initialization failed', [
            'response' => $response->json(),
            'data' => $data
        ]);

        return [
            'success' => false,
            'message' => $response->json()['message'] ?? 'Transaction initialization failed',
        ];
    }

    /**
     * Verify a transaction
     */
    public function verifyTransaction($reference)
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->secretKey,
        ])->get($this->baseUrl . '/transaction/verify/' . $reference);

        if ($response->successful()) {
            $data = $response->json()['data'];
            
            return [
                'success' => true,
                'status' => $data['status'],
                'amount' => $data['amount'] / 100, // Convert from kobo
                'currency' => $data['currency'],
                'transaction_date' => $data['transaction_date'],
                'reference' => $data['reference'],
                'authorization_code' => $data['authorization']['authorization_code'] ?? null,
                'card_type' => $data['authorization']['card_type'] ?? null,
                'bank' => $data['authorization']['bank'] ?? null,
                'customer' => $data['customer']['email'] ?? null,
                'metadata' => $data['metadata'] ?? [],
            ];
        }

        Log::error('Paystack verification failed', [
            'reference' => $reference,
            'response' => $response->json()
        ]);

        return [
            'success' => false,
            'message' => $response->json()['message'] ?? 'Verification failed',
        ];
    }

    /**
     * List banks (for bank transfer)
     */
    public function listBanks($country = 'Nigeria')
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->secretKey,
        ])->get($this->baseUrl . '/bank', [
            'country' => $country,
        ]);

        if ($response->successful()) {
            return [
                'success' => true,
                'data' => $response->json()['data'],
            ];
        }

        return [
            'success' => false,
            'message' => $response->json()['message'] ?? 'Failed to fetch banks',
        ];
    }

    /**
     * Generate a unique reference
     */
    public function generateReference()
    {
        return 'PAY-' . strtoupper(Str::random(10)) . '-' . time();
    }

    /**
     * Get public key
     */
    public function getPublicKey()
    {
        return $this->publicKey;
    }
}