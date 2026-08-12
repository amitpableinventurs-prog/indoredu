<?php

namespace App\Services\Payments;

use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Lightweight PayPal Orders v2 integration using the raw REST API (no SDK dependency).
 */
class PaypalGateway implements PaymentGateway
{
    protected function apiBase(): string
    {
        return config('services.paypal.mode') === 'live'
            ? 'https://api-m.paypal.com'
            : 'https://api-m.sandbox.paypal.com';
    }

    protected function accessToken(): string
    {
        return Cache::remember('paypal_access_token', 480, function () {
            $response = Http::asForm()
                ->withBasicAuth(config('services.paypal.client_id'), config('services.paypal.client_secret'))
                ->post($this->apiBase().'/v1/oauth2/token', ['grant_type' => 'client_credentials'])
                ->throw()
                ->json();

            return $response['access_token'];
        });
    }

    public function createCheckout(Payment $payment, string $successUrl, string $cancelUrl): string
    {
        $response = Http::withToken($this->accessToken())
            ->post($this->apiBase().'/v2/checkout/orders', [
                'intent' => 'CAPTURE',
                'purchase_units' => [[
                    'reference_id' => (string) $payment->id,
                    'custom_id' => (string) $payment->id,
                    'amount' => [
                        'currency_code' => $payment->currency,
                        'value' => number_format((float) $payment->amount, 2, '.', ''),
                    ],
                ]],
                'application_context' => [
                    'return_url' => $successUrl,
                    'cancel_url' => $cancelUrl,
                    'user_action' => 'PAY_NOW',
                ],
            ])
            ->throw()
            ->json();

        $payment->update([
            'gateway_transaction_id' => $response['id'] ?? null,
            'status' => Payment::STATUS_PROCESSING,
        ]);

        $approveLink = collect($response['links'] ?? [])->firstWhere('rel', 'approve');

        return $approveLink['href'] ?? $successUrl;
    }

    /**
     * Capture funds for an approved PayPal order. Called from the checkout return route.
     */
    public function captureOrder(Payment $payment): bool
    {
        $response = Http::withToken($this->accessToken())
            ->post($this->apiBase()."/v2/checkout/orders/{$payment->gateway_transaction_id}/capture");

        if ($response->successful()) {
            $data = $response->json();
            $captureId = $data['purchase_units'][0]['payments']['captures'][0]['id'] ?? null;

            $payment->update([
                'status' => Payment::STATUS_COMPLETED,
                'gateway_reference' => $captureId,
                'paid_at' => now(),
            ]);

            return true;
        }

        Log::error('PayPal capture failed', ['response' => $response->body()]);
        $payment->update(['status' => Payment::STATUS_FAILED]);

        return false;
    }

    public function handleWebhook(Request $request): ?Payment
    {
        if (! $this->verifyWebhook($request)) {
            Log::warning('PayPal webhook signature verification failed.');

            return null;
        }

        $event = $request->json()->all();
        $type = $event['event_type'] ?? null;
        $resource = $event['resource'] ?? [];

        $paymentId = $resource['custom_id']
            ?? $resource['purchase_units'][0]['custom_id']
            ?? null;

        if (! $paymentId) {
            return null;
        }

        $payment = Payment::find($paymentId);
        if (! $payment) {
            return null;
        }

        match ($type) {
            'PAYMENT.CAPTURE.COMPLETED' => $payment->update([
                'status' => Payment::STATUS_COMPLETED,
                'gateway_reference' => $resource['id'] ?? null,
                'paid_at' => now(),
            ]),
            'PAYMENT.CAPTURE.DENIED' => $payment->update(['status' => Payment::STATUS_FAILED]),
            'PAYMENT.CAPTURE.REFUNDED' => $payment->update(['status' => Payment::STATUS_REFUNDED, 'refunded_at' => now()]),
            default => null,
        };

        return $payment->fresh();
    }

    public function refund(Payment $payment, ?float $amount = null): bool
    {
        $captureId = $payment->gateway_reference;
        if (! $captureId) {
            return false;
        }

        $body = [];
        if ($amount) {
            $body['amount'] = [
                'value' => number_format($amount, 2, '.', ''),
                'currency_code' => $payment->currency,
            ];
        }

        $response = Http::withToken($this->accessToken())
            ->post($this->apiBase()."/v2/payments/captures/{$captureId}/refund", $body);

        if ($response->successful()) {
            $payment->update([
                'status' => $amount && $amount < $payment->amount
                    ? Payment::STATUS_PARTIALLY_REFUNDED
                    : Payment::STATUS_REFUNDED,
                'refunded_at' => now(),
            ]);

            return true;
        }

        Log::error('PayPal refund failed', ['response' => $response->body()]);

        return false;
    }

    protected function verifyWebhook(Request $request): bool
    {
        $webhookId = config('services.paypal.webhook_id');
        if (! $webhookId) {
            // No webhook ID configured (e.g. local/dev) — skip verification.
            return true;
        }

        $response = Http::withToken($this->accessToken())
            ->post($this->apiBase().'/v1/notifications/verify-webhook-signature', [
                'auth_algo' => $request->header('Paypal-Auth-Algo'),
                'cert_url' => $request->header('Paypal-Cert-Url'),
                'transmission_id' => $request->header('Paypal-Transmission-Id'),
                'transmission_sig' => $request->header('Paypal-Transmission-Sig'),
                'transmission_time' => $request->header('Paypal-Transmission-Time'),
                'webhook_id' => $webhookId,
                'webhook_event' => $request->json()->all(),
            ]);

        return $response->successful() && ($response->json('verification_status') === 'SUCCESS');
    }
}
