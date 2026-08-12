<?php

namespace App\Http\Controllers;

use App\Models\ContentReport;
use App\Models\Message;
use App\Models\Review;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ContentReportController extends Controller
{
    /** @var array<string, class-string> */
    protected array $reportableTypes = [
        'user' => User::class,
        'review' => Review::class,
        'message' => Message::class,
    ];

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'reportable_type' => ['required', Rule::in(array_keys($this->reportableTypes))],
            'reportable_id' => ['required', 'integer'],
            'reason' => ['required', 'string', 'max:255'],
            'details' => ['nullable', 'string', 'max:2000'],
        ]);

        $modelClass = $this->reportableTypes[$data['reportable_type']];
        $model = $modelClass::findOrFail($data['reportable_id']);

        ContentReport::create([
            'reporter_id' => $request->user()->id,
            'reportable_type' => $modelClass,
            'reportable_id' => $model->id,
            'reason' => $data['reason'],
            'details' => $data['details'] ?? null,
        ]);

        return back()->with('status', 'Thanks — our moderation team will review this report.');
    }
}
