<?php

namespace App\Http\Controllers\Tutor;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $from = $request->date('from') ?? now()->subMonths(3);
        $to = $request->date('to') ?? now();

        $bookings = Booking::where('tutor_id', $user->id)
            ->whereBetween('scheduled_date', [$from->toDateString(), $to->toDateString()])
            ->with(['student', 'subject', 'attendance'])
            ->orderBy('scheduled_date')
            ->get();

        $bySubject = $bookings->where('status', Booking::STATUS_COMPLETED)->groupBy('subject.name')->map->count();

        $summary = [
            'total_sessions' => $bookings->count(),
            'completed' => $bookings->where('status', Booking::STATUS_COMPLETED)->count(),
            'cancelled' => $bookings->where('status', Booking::STATUS_CANCELLED)->count(),
            'no_show' => $bookings->where('status', Booking::STATUS_NO_SHOW)->count(),
            'unique_students' => $bookings->pluck('student_id')->unique()->count(),
            'earnings' => Payment::where('payee_id', $user->id)->where('status', 'completed')
                ->whereBetween('paid_at', [$from, $to])->sum('net_amount'),
            'hours_taught' => round($bookings->where('status', Booking::STATUS_COMPLETED)->sum('duration_minutes') / 60, 1),
        ];

        return view('tutor.reports.index', compact('bookings', 'summary', 'bySubject', 'from', 'to'));
    }

    public function export(Request $request)
    {
        $user = $request->user();
        $from = $request->date('from') ?? now()->subMonths(3);
        $to = $request->date('to') ?? now();

        $bookings = Booking::where('tutor_id', $user->id)
            ->whereBetween('scheduled_date', [$from->toDateString(), $to->toDateString()])
            ->with(['student', 'subject'])
            ->orderBy('scheduled_date')
            ->get();

        $csv = "Date,Time,Student,Subject,Duration (min),Price,Status\n";
        foreach ($bookings as $b) {
            $csv .= implode(',', [
                $b->scheduled_date->toDateString(),
                $b->start_time,
                '"'.$b->student->name.'"',
                '"'.($b->subject->name ?? '').'"',
                $b->duration_minutes,
                $b->price,
                $b->status,
            ])."\n";
        }

        return Response::make($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="tutor-report-'.now()->format('Y-m-d').'.csv"',
        ]);
    }
}
