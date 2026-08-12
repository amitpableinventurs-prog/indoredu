<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use App\Notifications\NewMessageNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MessageController extends Controller
{
    public function index(Request $request): View
    {
        $conversations = $request->user()->conversations()
            ->with(['participants', 'latestMessage'])
            ->orderByDesc('last_message_at')
            ->paginate(15);

        return view('messages.index', compact('conversations'));
    }

    public function show(Request $request, Conversation $conversation): View
    {
        $this->authorize('view', $conversation);

        $conversation->load(['messages.sender', 'participants']);

        $conversation->participants()->updateExistingPivot($request->user()->id, ['last_read_at' => now()]);

        return view('messages.show', compact('conversation'));
    }

    public function store(Request $request, Conversation $conversation): RedirectResponse
    {
        $this->authorize('sendMessage', $conversation);

        $data = $request->validate([
            'body' => ['required_without:attachment', 'nullable', 'string', 'max:5000'],
            'attachment' => ['nullable', 'file', 'max:10240'],
        ]);

        $attachmentPath = null;
        $attachmentName = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('message-attachments', 'local');
            $attachmentName = $request->file('attachment')->getClientOriginalName();
        }

        $message = Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $request->user()->id,
            'body' => $data['body'] ?? null,
            'attachment_path' => $attachmentPath,
            'attachment_name' => $attachmentName,
        ]);

        $conversation->update(['last_message_at' => now()]);
        $conversation->participants()->updateExistingPivot($request->user()->id, ['last_read_at' => now()]);

        foreach ($conversation->participants as $participant) {
            if ($participant->id !== $request->user()->id) {
                $participant->notify(new NewMessageNotification($message));
            }
        }

        return back();
    }

    /**
     * Start (or resume) a direct conversation with another user, e.g. a
     * student messaging a tutor from their profile page before booking.
     */
    public function start(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'user_id' => ['required', 'exists:users,id', 'different:'.$request->user()->id],
            'body' => ['required', 'string', 'max:5000'],
        ]);

        $otherUser = User::findOrFail($data['user_id']);

        if ($otherUser->hasBlocked($request->user()) || $request->user()->hasBlocked($otherUser)) {
            abort(403, 'You are unable to message this user.');
        }

        $conversation = Conversation::whereNull('booking_id')
            ->whereHas('participants', fn ($q) => $q->where('user_id', $request->user()->id))
            ->whereHas('participants', fn ($q) => $q->where('user_id', $otherUser->id))
            ->first();

        if (! $conversation) {
            $conversation = Conversation::create(['subject' => "Chat with {$otherUser->name}"]);
            $conversation->participants()->attach([$request->user()->id, $otherUser->id]);
        }

        $message = Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $request->user()->id,
            'body' => $data['body'],
        ]);

        $conversation->update(['last_message_at' => now()]);
        $otherUser->notify(new NewMessageNotification($message));

        return redirect()->route('messages.show', $conversation);
    }
}
