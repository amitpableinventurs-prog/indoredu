<?php

namespace App\Services\Payments;

use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Lightweight Stripe integration using the raw REST API (no SDK dependency),
 * so the platform stays pluggable without pinning to a specific client library.
 */
class StripeGateway implements PaymentGateway
{
    protected string $apiBase = 'https://api.stripe.com/v1';

    protected function secretKey(): string
    {
        return (string) config('services.stripe.secret');
    }

    protected function webhookSecret(): string
    {
        return (string) config('services.stripe.webhook_secret');
    }

    public function createCheckout(Payment $payment, string $successUrl, string $cancelUrl): string
    {
        $response = Http::asForm()
            ->withToken($this->secretKey())
            ->post("{$this->apiBase}/checkout/sessions", [
                'mode' => 'payment',
                'success_url' => $successUrl,
                'cancel_url' => $cancelUrl,
                'customer_email' => $payment->payer->email,
                'client_reference_id' => (string) $payment->id,
                'line_items' => [[
                    'quantity' => 1,
                    'price_data' => [
                        'currency' => strtolower($payment->currency),
                        'unit_amount' => (int) round($payment->amount * 100),
                        'product_data' => [
                            'name' => $this->describe($payment),
                        ],
                    ],
                ]],
                'metadata' => [
                    'payment_id' => $payment->id,
                ],
            ])
            ->throw()
            ->json();

        $payment->update([
            'gateway_transaction_id' => $response['id'] ?? null,
            'status' => Payment::STATUS_PROCESSING,
        ]);

        return $response['url'];
    }

    public function handleWebhook(Request $request): ?Payment
    {
        if (! $this->verifySignature($request)) {
            Log::warning('Stripe webhook signature verification failed.');

            return null;
        }

        $event = $request->json()->all();
        $type = $event['type'] ?? null;
        $object = $event['data']['object'] ?? [];

        $paymentId = $object['metadata']['payment_id'] ?? $object['client_reference_id'] ?? null;
        if (! $paymentId) {
            return null;
        }

        $payment = Payment::find($paymentId);
        if (! $payment) {
            return null;
        }

        match ($type) {
            'checkout.session.completed', 'payment_intent.succeeded' => $payment->update([
                'status' => Payment::STATUS_COMPLETED,
                'gateway_reference' => $object['payment_intent'] ?? $object['id'] ?? null,
                'paid_at' => now(),
            ]),
            'payment_intent.payment_failed' => $payment->update(['status' => Payment::STATUS_FAILED]),
            'charge.refunded' => $payment->update(['status' => Payment::STATUS_REFUNDED, 'refunded_at' => now()]),
            default => null,
        };

        return $payment->fresh();
    }

    public function refund(Payment $payment, ?float $amount = null): bool
    {
        $params = ['payment_intent' => $payment->gateway_reference ?? $payment->gateway_transaction_id];
        if ($amount) {
            $params['amount'] = (int) round($amount * 100);
        }

        $response = Http::asForm()->withToken($this->secretKey())
            ->post("{$this->apiBase}/refunds", $params);

        if ($response->successful()) {
            $payment->update([
                'status' => $amount && $amount < $payment->amount
                    ? Payment::STATUS_PARTIALLY_REFUNDED
                    : Payment::STATUS_REFUNDED,
                'refunded_at' => now(),
            ]);

            return true;
        }

        Log::error('Stripe refund failed', ['response' => $response->body()]);

        return false;
    }

    protected function verifySignature(Request $request): bool
    {
        $secret = $this->webhookSecret();
        if (! $secret) {
            // No webhook secret configured (e.g. local/dev) — skip verification.
            return true;
        }

        $signatureHeader = $request->header('Stripe-Signature', '');
        $payload = $request->getContent();

        $parts = [];
        foreach (explode(',', $signatureHeader) as $pair) {
            [$key, $value] = array_pad(explode('=', $pair, 2), 2, null);
            $parts[$key][] = $value;
        }

        $timestamp = $parts['t'][0] ?? null;
        $signatures = $parts['v1'] ?? [];

        if (! $timestamp || empty($signatures)) {
            return false;
        }

        $expected = hash_hmac('sha256', "{$timestamp}.{$payload}", $secret);

        foreach ($signatures as $signature) {
            if (hash_equals($expected, (string) $signature)) {
                return true;
            }
        }

        return false;
    }

    protected function describe(Payment $payment): string
    {
        if ($payment->booking) {
            return 'Tutoring session — '.($payment->booking->subject->name ?? 'Booking #'.$payment->booking_id);
        }

        if ($payment->courseEnrollment) {
            return 'Course enrollment — '.($payment->courseEnrollment->course->title ?? '');
        }

        return 'IndorEdu payment';
    }
}
