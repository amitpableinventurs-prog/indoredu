<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Booking;
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

        $bookings = Booking::where('student_id', $user->id)
            ->whereBetween('scheduled_date', [$from->toDateString(), $to->toDateString()])
            ->with(['tutor', 'subject', 'attendance', 'review'])
            ->orderBy('scheduled_date')
            ->get();

        $bySubject = $bookings->where('status', Booking::STATUS_COMPLETED)->groupBy('subject.name')->map->count();

        $summary = [
            'total_sessions' => $bookings->count(),
            'completed' => $bookings->where('status', Booking::STATUS_COMPLETED)->count(),
            'cancelled' => $bookings->where('status', Booking::STATUS_CANCELLED)->count(),
            'hours_learned' => round($bookings->where('status', Booking::STATUS_COMPLETED)->sum('duration_minutes') / 60, 1),
            'attendance_rate' => $this->attendanceRate($bookings),
            'total_spent' => \App\Models\Payment::where('payer_id', $user->id)->where('status', 'completed')
                ->whereBetween('paid_at', [$from, $to])->sum('amount'),
        ];

        return view('student.reports.index', compact('bookings', 'summary', 'bySubject', 'from', 'to'));
    }

    public function export(Request $request)
    {
        $user = $request->user();
        $from = $request->date('from') ?? now()->subMonths(3);
        $to = $request->date('to') ?? now();

        $bookings = Booking::where('student_id', $user->id)
            ->whereBetween('scheduled_date', [$from->toDateString(), $to->toDateString()])
            ->with(['tutor', 'subject', 'attendance'])
            ->orderBy('scheduled_date')
            ->get();

        $csv = "Date,Time,Tutor,Subject,Duration (min),Status,Attendance,Progress Notes\n";
        foreach ($bookings as $b) {
            $csv .= implode(',', [
                $b->scheduled_date->toDateString(),
                $b->start_time,
                '"'.$b->tutor->name.'"',
                '"'.($b->subject->name ?? '').'"',
                $b->duration_minutes,
                $b->status,
                $b->attendance->student_status ?? '',
                '"'.str_replace('"', '""', $b->tutor_notes ?? '').'"',
            ])."\n";
        }

        return Response::make($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="learning-report-'.now()->format('Y-m-d').'.csv"',
        ]);
    }

    protected function attendanceRate($bookings): float
    {
        $completed = $bookings->where('status', Booking::STATUS_COMPLETED)->filter(fn ($b) => $b->attendance);
        if ($completed->isEmpty()) {
            return 0;
        }

        $present = $completed->filter(fn ($b) => in_array($b->attendance->student_status, ['present', 'late']))->count();

        return round(($present / $completed->count()) * 100, 1);
    }
}
