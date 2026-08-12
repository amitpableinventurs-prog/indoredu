<?php

namespace App\Http\Controllers\Tutor;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BookingController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->query('status', 'upcoming');

        $query = $request->user()->bookingsAsTutor()->with(['student', 'subject', 'review']);

        match ($status) {
            'upcoming' => $query->whereIn('status', [Booking::STATUS_PENDING, Booking::STATUS_CONFIRMED])
                ->where('scheduled_date', '>=', now()->toDateString())
                ->orderBy('scheduled_date')->orderBy('start_time'),
            'completed' => $query->where('status', Booking::STATUS_COMPLETED)->latest('scheduled_date'),
            'cancelled' => $query->where('status', Booking::STATUS_CANCELLED)->latest('scheduled_date'),
            default => $query->latest('scheduled_date'),
        };

        $bookings = $query->paginate(15)->withQueryString();

        return view('tutor.bookings.index', compact('bookings', 'status'));
    }
}
