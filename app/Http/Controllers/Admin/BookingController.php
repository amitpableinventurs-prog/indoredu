<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BookingController extends Controller
{
    public function index(Request $request): View
    {
        $query = Booking::with(['student', 'tutor', 'subject', 'payment']);

        if ($status = $request->string('status')->value()) {
            $query->where('status', $status);
        }

        if ($search = $request->string('q')->trim()->value()) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('student', fn ($s) => $s->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('tutor', fn ($t) => $t->where('name', 'like', "%{$search}%"));
            });
        }

        $bookings = $query->latest('scheduled_date')->paginate(25)->withQueryString();

        return view('admin.bookings.index', compact('bookings'));
    }

    public function show(Booking $booking): View
    {
        $booking->load(['student', 'tutor', 'subject', 'attendance', 'review', 'payment', 'conversation.messages']);

        return view('admin.bookings.show', compact('booking'));
    }
}
