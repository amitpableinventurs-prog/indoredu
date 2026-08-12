<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminActionLog;
use Illuminate\View\View;

class ActivityLogController extends Controller
{
    public function index(): View
    {
        $logs = AdminActionLog::with('admin')->latest()->paginate(30);

        return view('admin.activity-log', compact('logs'));
    }
}
