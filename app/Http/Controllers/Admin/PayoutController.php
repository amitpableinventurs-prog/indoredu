<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminActionLog;
use App\Models\Payout;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PayoutController extends Controller
{
    public function index(Request $request): View
    {
        $query = Payout::with('tutorProfile.user');

        if ($status = $request->string('status')->value()) {
            $query->where('status', $status);
        }

        $payouts = $query->latest()->paginate(25)->withQueryString();

        return view('admin.payouts.index', compact('payouts'));
    }

    public function markPaid(Request $request, Payout $payout): RedirectResponse
    {
        $payout->update(['status' => 'paid', 'processed_at' => now()]);
        AdminActionLog::record($request->user(), 'payout.paid', $payout);

        return back()->with('status', 'Payout marked as paid.');
    }

    public function reject(Request $request, Payout $payout): RedirectResponse
    {
        $data = $request->validate(['notes' => ['nullable', 'string', 'max:500']]);
        $payout->update(['status' => 'failed', 'notes' => $data['notes'] ?? null, 'processed_at' => now()]);
        AdminActionLog::record($request->user(), 'payout.rejected', $payout, $data);

        return back()->with('status', 'Payout rejected.');
    }
}
