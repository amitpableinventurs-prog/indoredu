<?php

namespace App\Http\Controllers\Tutor;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Payout;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PayoutController extends Controller
{
    public function index(Request $request): View
    {
        $profile = $request->user()->tutorProfile;

        $earned = Payment::where('payee_id', $request->user()->id)->where('status', 'completed')->sum('net_amount');
        $paidOut = $profile->payouts()->where('status', 'paid')->sum('amount');
        $pendingPayout = $profile->payouts()->whereIn('status', ['pending', 'processing'])->sum('amount');
        $available = max(0, $earned - $paidOut - $pendingPayout);

        $payouts = $profile->payouts()->latest()->paginate(15);

        return view('tutor.payouts', compact('payouts', 'earned', 'available', 'paidOut'));
    }

    public function store(Request $request): RedirectResponse
    {
        $profile = $request->user()->tutorProfile;

        $earned = Payment::where('payee_id', $request->user()->id)->where('status', 'completed')->sum('net_amount');
        $paidOut = $profile->payouts()->where('status', 'paid')->sum('amount');
        $pendingPayout = $profile->payouts()->whereIn('status', ['pending', 'processing'])->sum('amount');
        $available = max(0, $earned - $paidOut - $pendingPayout);

        $data = $request->validate([
            'amount' => ['required', 'numeric', 'min:1'],
            'method' => ['required', 'in:bank_transfer,paypal'],
        ]);

        if ($data['amount'] > $available) {
            return back()->withErrors(['amount' => 'Requested amount exceeds your available balance of ₹'.number_format($available, 2).'.']);
        }

        Payout::create([
            'tutor_profile_id' => $profile->id,
            'amount' => $data['amount'],
            'currency' => config('payments.currency', 'INR'),
            'method' => $data['method'],
            'status' => 'pending',
            'requested_at' => now(),
        ]);

        return back()->with('status', 'Payout requested — funds are typically processed within 5 business days.');
    }
}
