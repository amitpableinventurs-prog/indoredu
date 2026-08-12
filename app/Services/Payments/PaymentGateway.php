<?php

namespace App\Services\Payments;

use App\Models\Payment;
use Illuminate\Http\Request;

interface PaymentGateway
{
    /**
     * Start a hosted checkout for the given payment and return a redirect URL.
     */
    public function createCheckout(Payment $payment, string $successUrl, string $cancelUrl): string;

    /**
     * Handle an incoming webhook request, update the related Payment, and return it.
     */
    public function handleWebhook(Request $request): ?Payment;

    /**
     * Refund a completed payment (fully or partially).
     */
    public function refund(Payment $payment, ?float $amount = null): bool;
}
