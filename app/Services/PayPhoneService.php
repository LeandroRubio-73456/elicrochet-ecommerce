<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PayPhoneService
{
    protected string $baseUrl = 'https://pay.payphonetodoesposible.com/api/button';

    protected string $token;

    /**
     * Modo simulación: cuando está activo no se llama a la API real de
     * PayPhone. Se usa cuando no hay credenciales de comercio disponibles
     * (por ejemplo, en un entorno de demostración o portafolio).
     */
    protected bool $simulate;

    public function __construct()
    {
        $this->token = (string) config('services.payphone.token', '');

        $configuredSimulate = config('services.payphone.simulate');
        $this->simulate = $configuredSimulate !== null
            ? (bool) $configuredSimulate
            : $this->token === '';
    }

    /**
     * Prepare a payment link (Prepare v1)
     */
    public function prepare(int $amount, string $clientTransactionId, ?string $responseUrl = null, ?string $cancellationUrl = null): array
    {
        if ($this->simulate) {
            return [
                'paymentId' => random_int(100000, 999999),
                'payWithCard' => route('checkout.simulate', [
                    'transactionId' => $clientTransactionId,
                    'amount' => $amount,
                ]),
            ];
        }

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
    public function confirm(int $id, string $clientTransactionId, ?string $simulatedStatus = null): array
    {
        if ($this->simulate) {
            return [
                'transactionStatus' => $simulatedStatus ?? 'Approved',
                'id' => $id,
                'clientTransactionId' => $clientTransactionId,
            ];
        }

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
