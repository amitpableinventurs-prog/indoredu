<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminActionLog;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $query = User::query()->withCount(['bookingsAsStudent', 'bookingsAsTutor']);

        if ($role = $request->string('role')->value()) {
            $query->where('role', $role);
        }

        if ($status = $request->string('status')->value()) {
            $query->where('status', $status);
        }

        if ($search = $request->string('q')->trim()->value()) {
            $query->where(fn ($q) => $q->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%"));
        }

        $users = $query->latest()->paginate(20)->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    public function show(User $user): View
    {
        $user->load(['tutorProfile.subjects', 'studentProfile']);

        return view('admin.users.show', compact('user'));
    }

    public function updateStatus(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', Rule::in([User::STATUS_ACTIVE, User::STATUS_SUSPENDED, User::STATUS_BANNED])],
            'reason' => ['nullable', 'string', 'max:500'],
        ]);

        abort_if($user->isAdmin(), 422, 'Admin accounts cannot be modified here.');

        $user->update(['status' => $data['status']]);

        AdminActionLog::record($request->user(), "user.status.{$data['status']}", $user, ['reason' => $data['reason'] ?? null]);

        return back()->with('status', "{$user->name}'s account is now {$data['status']}.");
    }
}
