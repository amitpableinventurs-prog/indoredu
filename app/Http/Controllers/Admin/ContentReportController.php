<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminActionLog;
use App\Models\ContentReport;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ContentReportController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->query('status', 'pending');

        $reports = ContentReport::with(['reporter', 'reportable'])
            ->when($status !== 'all', fn ($q) => $q->where('status', $status))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.reports.index', compact('reports', 'status'));
    }

    public function resolve(Request $request, ContentReport $report): RedirectResponse
    {
        $data = $request->validate(['resolution_notes' => ['nullable', 'string', 'max:1000']]);

        $report->update([
            'status' => ContentReport::STATUS_RESOLVED,
            'resolved_by' => $request->user()->id,
            'resolved_at' => now(),
            'resolution_notes' => $data['resolution_notes'] ?? null,
        ]);

        AdminActionLog::record($request->user(), 'report.resolved', $report);

        return back()->with('status', 'Report resolved.');
    }

    public function dismiss(Request $request, ContentReport $report): RedirectResponse
    {
        $report->update([
            'status' => ContentReport::STATUS_DISMISSED,
            'resolved_by' => $request->user()->id,
            'resolved_at' => now(),
        ]);

        AdminActionLog::record($request->user(), 'report.dismissed', $report);

        return back()->with('status', 'Report dismissed.');
    }
}
