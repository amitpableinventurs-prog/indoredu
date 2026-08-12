<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Notifications\BookingStatusNotification;
use App\Services\Payments\PaymentManager;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class WebhookController extends Controller
{
    public function __construct(protected PaymentManager $payments) {}

    public function handle(Request $request, string $gateway): Response
    {
        $payment = $this->payments->gateway($gateway)->handleWebhook($request);

        if ($payment && $payment->isCompleted() && $payment->booking && $payment->booking->status === Booking::STATUS_PENDING) {
            $payment->booking->update(['status' => Booking::STATUS_CONFIRMED, 'confirmed_at' => now()]);
            $payment->booking->tutor->notify(new BookingStatusNotification($payment->booking, 'created'));
        }

        return response()->noContent();
    }
}
