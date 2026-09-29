<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Course;
use App\Models\Enquiry;
use App\Models\Message;
use App\Models\TutorProfile;
use App\Models\User;
use App\Notifications\EnquiryNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class EnquiryController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $status = $request->query('status', 'open');

        $query = Enquiry::with(['student', 'tutor', 'subject', 'course']);

        if ($user->isStudent()) {
            $query->where('student_id', $user->id);
        } elseif ($user->isTutor()) {
            $query->where('tutor_id', $user->id);
        } elseif (! $user->isAdmin()) {
            abort(403);
        }

        match ($status) {
            'open' => $query->whereIn('status', [Enquiry::STATUS_PENDING, Enquiry::STATUS_REPLIED]),
            'pending' => $query->where('status', Enquiry::STATUS_PENDING),
            'replied' => $query->where('status', Enquiry::STATUS_REPLIED),
            'closed' => $query->whereIn('status', [Enquiry::STATUS_CLOSED, Enquiry::STATUS_DECLINED]),
            default => null,
        };

        $enquiries = $query->latest()->paginate(15)->withQueryString();

        return view('enquiries.index', compact('enquiries', 'status'));
    }

    public function show(Request $request, Enquiry $enquiry): View
    {
        $this->authorize('view', $enquiry);

        $enquiry->load(['student.studentProfile', 'tutor.tutorProfile', 'subject', 'course']);

        return view('enquiries.show', compact('enquiry'));
    }

    public function store(Request $request): RedirectResponse
    {
        $student = $request->user();
        abort_unless($student->isStudent(), 403, 'Only student accounts can send enquiries.');

        $data = $request->validate([
            'tutor_id' => ['required', 'exists:users,id'],
            'subject_id' => ['nullable', 'exists:subjects,id'],
            'course_id' => ['nullable', 'exists:courses,id'],
            'title' => ['required', 'string', 'max:150'],
            'message' => ['required', 'string', 'max:3000'],
            'grade' => ['nullable', Rule::in(array_keys(Course::GRADES))],
            'preferred_mode' => ['nullable', Rule::in(array_keys(Enquiry::MODES))],
            'preferred_time' => ['nullable', 'string', 'max:100'],
        ]);

        $tutor = User::findOrFail($data['tutor_id']);
        $profile = TutorProfile::approved()->where('user_id', $tutor->id)->first();
        abort_unless($tutor->isTutor() && $profile, 404);

        if ($tutor->hasBlocked($student) || $student->hasBlocked($tutor)) {
            abort(403, 'You are unable to contact this tutor.');
        }

        if (! empty($data['course_id']) && ! $profile->courses()->whereKey($data['course_id'])->exists()) {
            $data['course_id'] = null;
        }

        $enquiry = Enquiry::create($data + [
            'student_id' => $student->id,
            'status' => Enquiry::STATUS_PENDING,
        ]);

        $tutor->notify(new EnquiryNotification($enquiry, 'received'));

        return redirect()->route('enquiries.show', $enquiry)
            ->with('status', 'Your enquiry has been sent. The tutor will get back to you soon.');
    }

    /**
     * Tutor answers the enquiry. The reply also opens (or reuses) the direct
     * conversation between the two so they can keep talking in Messages.
     */
    public function reply(Request $request, Enquiry $enquiry): RedirectResponse
    {
        $this->authorize('reply', $enquiry);

        $data = $request->validate([
            'tutor_reply' => ['required', 'string', 'max:3000'],
        ]);

        DB::transaction(function () use ($enquiry, $data) {
            $conversation = Conversation::whereNull('booking_id')
                ->whereHas('participants', fn ($q) => $q->where('user_id', $enquiry->student_id))
                ->whereHas('participants', fn ($q) => $q->where('user_id', $enquiry->tutor_id))
                ->first();

            if (! $conversation) {
                $conversation = Conversation::create(['subject' => "Enquiry: {$enquiry->title}"]);
                $conversation->participants()->attach([$enquiry->student_id, $enquiry->tutor_id]);
            }

            Message::create([
                'conversation_id' => $conversation->id,
                'sender_id' => $enquiry->student_id,
                'body' => "Enquiry: {$enquiry->title}\n\n{$enquiry->message}",
            ]);

            Message::create([
                'conversation_id' => $conversation->id,
                'sender_id' => $enquiry->tutor_id,
                'body' => $data['tutor_reply'],
            ]);

            $conversation->update(['last_message_at' => now()]);
            $conversation->participants()->updateExistingPivot($enquiry->tutor_id, ['last_read_at' => now()]);

            $enquiry->update([
                'tutor_reply' => $data['tutor_reply'],
                'status' => Enquiry::STATUS_REPLIED,
                'replied_at' => now(),
                'conversation_id' => $conversation->id,
            ]);
        });

        $enquiry->student->notify(new EnquiryNotification($enquiry, 'replied'));

        return back()->with('status', 'Reply sent. You can continue the conversation in Messages.');
    }

    public function decline(Request $request, Enquiry $enquiry): RedirectResponse
    {
        $this->authorize('decline', $enquiry);

        $data = $request->validate([
            'tutor_reply' => ['nullable', 'string', 'max:1000'],
        ]);

        $enquiry->update([
            'tutor_reply' => $data['tutor_reply'] ?? null,
            'status' => Enquiry::STATUS_DECLINED,
            'replied_at' => now(),
            'closed_at' => now(),
        ]);

        $enquiry->student->notify(new EnquiryNotification($enquiry, 'declined'));

        return back()->with('status', 'Enquiry declined.');
    }

    public function close(Request $request, Enquiry $enquiry): RedirectResponse
    {
        $this->authorize('close', $enquiry);

        $enquiry->update([
            'status' => Enquiry::STATUS_CLOSED,
            'closed_at' => now(),
        ]);

        return back()->with('status', 'Enquiry marked as closed.');
    }
}
