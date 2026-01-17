<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PayPhoneService
{
    protected string $baseUrl = 'https://pay.payphonetodoesposible.com/api/button';

    protected string $token;

    public function __construct()
    {
        $this->token = config('services.payphone.token');
    }

    /**
     * Prepare a payment link (Prepare v1)
     */
    public function prepare(int $amount, string $clientTransactionId, ?string $responseUrl = null, ?string $cancellationUrl = null): array
    {
        $response = Http::withoutVerifying()
            ->withToken($this->token)
            ->post("{$this->baseUrl}/Prepare", [
                'amount' => $amount,
                'amountWithoutTax' => $amount,
                'amountWithTax' => 0,
                'tax' => 0,
                'serviceTax' => 0,
                'tip' => 0,
                'currency' => 'USD',
                'clientTransactionId' => $clientTransactionId,
                'responseUrl' => $responseUrl ?? route('checkout.callback'),
                'cancellationUrl' => $cancellationUrl ?? route('checkout.cancel'),
            ]);

        if (! $response->successful()) {
            Log::error('PayPhone Prepare Failed: '.$response->body());
            throw new \Exception('Error al generar el link de pago: '.$response->body());
        }

        return $response->json();
    }

    /**
     * Confirm a payment (Confirm v1)
     */
    public function confirm(int $id, string $clientTransactionId): array
    {
        $response = Http::withoutVerifying()
            ->withToken($this->token)
            ->post("{$this->baseUrl}/Confirm", [
                'id' => $id,
                'clientTransactionId' => $clientTransactionId,
            ]);

        if (! $response->successful()) {
            Log::error('PayPhone Confirm Failed: '.$response->body());
        }

        return $response->json();
    }
}
