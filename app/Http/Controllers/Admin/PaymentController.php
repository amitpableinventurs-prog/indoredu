<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminActionLog;
use App\Models\Payment;
use App\Services\Payments\PaymentManager;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PaymentController extends Controller
{
    public function __construct(protected PaymentManager $payments) {}

    public function index(Request $request): View
    {
        $query = Payment::with(['payer', 'payee', 'booking']);

        if ($status = $request->string('status')->value()) {
            $query->where('status', $status);
        }

        if ($gateway = $request->string('gateway')->value()) {
            $query->where('gateway', $gateway);
        }

        $payments = $query->latest()->paginate(25)->withQueryString();

        return view('admin.payments.index', compact('payments'));
    }

    public function refund(Request $request, Payment $payment): RedirectResponse
    {
        abort_unless($payment->isCompleted(), 422, 'Only completed payments can be refunded.');

        try {
            $ok = $this->payments->gateway($payment->gateway)->refund($payment);
        } catch (\Throwable $e) {
            report($e);
            $ok = false;
        }

        if ($ok) {
            AdminActionLog::record($request->user(), 'payment.refunded', $payment);

            return back()->with('status', 'Payment refunded.');
        }

        return back()->with('error', 'Refund failed — the payment gateway could not be reached or declined the request. Check logs for details.');
    }
}
