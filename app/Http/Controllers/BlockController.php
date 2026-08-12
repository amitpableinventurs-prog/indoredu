<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserBlock;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class BlockController extends Controller
{
    public function store(Request $request, User $user): RedirectResponse
    {
        abort_if($request->user()->id === $user->id, 422, 'You cannot block yourself.');

        UserBlock::firstOrCreate([
            'blocker_id' => $request->user()->id,
            'blocked_id' => $user->id,
        ]);

        return back()->with('status', "{$user->name} has been blocked. They can no longer message or book you.");
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        UserBlock::where('blocker_id', $request->user()->id)->where('blocked_id', $user->id)->delete();

        return back()->with('status', "{$user->name} has been unblocked.");
    }
}
