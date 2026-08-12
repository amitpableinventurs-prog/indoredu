<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Payment;
use App\Notifications\BookingStatusNotification;
use App\Services\Payments\PaymentManager;
use App\Services\Payments\PaypalGateway;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function __construct(protected PaymentManager $payments) {}

    public function checkout(Request $request, Payment $payment)
    {
        abort_unless($request->user()->id === $payment->payer_id, 403);

        if ($payment->isCompleted()) {
            return redirect()->route('bookings.show', $payment->booking_id)->with('status', 'This payment was already completed.');
        }

        $gatewayName = $request->query('gateway', $payment->gateway);
        if (in_array($gatewayName, $this->payments->availableGateways()) && $gatewayName !== $payment->gateway) {
            $payment->update(['gateway' => $gatewayName]);
        }

        return view('payments.checkout', [
            'payment' => $payment,
            'gateways' => $this->payments->availableGateways(),
        ]);
    }

    public function redirect(Request $request, Payment $payment)
    {
        abort_unless($request->user()->id === $payment->payer_id, 403);

        $gateway = $this->payments->gateway($payment->gateway);

        try {
            $url = $gateway->createCheckout(
                $payment,
                route('payments.return', $payment),
                route('payments.cancel', $payment),
            );
        } catch (\Throwable $e) {
            report($e);

            return redirect()->route('payments.checkout', $payment)
                ->with('error', 'The payment provider could not be reached right now. Please try again shortly.');
        }

        return redirect()->away($url);
    }

    public function return(Request $request, Payment $payment)
    {
        abort_unless($request->user()->id === $payment->payer_id, 403);

        if ($payment->gateway === 'paypal' && ! $payment->isCompleted()) {
            try {
                app(PaypalGateway::class)->captureOrder($payment);
            } catch (\Throwable $e) {
                report($e);
            }
        }

        $payment->refresh();

        if ($payment->isCompleted() && $payment->booking) {
            $payment->booking->update(['status' => Booking::STATUS_CONFIRMED, 'confirmed_at' => now()]);
            $payment->booking->tutor->notify(new BookingStatusNotification($payment->booking, 'created'));

            return redirect()->route('bookings.show', $payment->booking)->with('status', 'Payment successful — your session is confirmed!');
        }

        if ($payment->courseEnrollment) {
            return redirect()->route('student.courses.index')->with('status', 'Payment successful — you are enrolled!');
        }

        return redirect()->route('payments.checkout', $payment)->with('error', 'We could not confirm your payment yet. It may still be processing.');
    }

    public function cancel(Request $request, Payment $payment)
    {
        abort_unless($request->user()->id === $payment->payer_id, 403);

        return redirect()->route('payments.checkout', $payment)->with('error', 'Payment was cancelled. You can try again below.');
    }

    public function receipt(Request $request, Payment $payment)
    {
        $user = $request->user();
        abort_unless($user->isAdmin() || $user->id === $payment->payer_id || $user->id === $payment->payee_id, 403);
        abort_unless($payment->isCompleted(), 404);

        $payment->load(['payer', 'payee', 'booking.subject', 'courseEnrollment.course']);

        return view('payments.receipt', compact('payment'));
    }

    public function history(Request $request)
    {
        $payments = $request->user()->paymentsMade()
            ->with(['booking.subject', 'booking.tutor', 'courseEnrollment.course'])
            ->latest()
            ->paginate(20);

        return view('payments.history', compact('payments'));
    }
}
