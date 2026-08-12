<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingsController extends Controller
{
    public function edit(Request $request): View
    {
        $preferences = $request->user()->notificationPreference ?? $request->user()->notificationPreference()->create();

        return view('settings.notifications', compact('preferences'));
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'email_bookings' => ['sometimes', 'boolean'],
            'email_messages' => ['sometimes', 'boolean'],
            'email_reminders' => ['sometimes', 'boolean'],
            'email_marketing' => ['sometimes', 'boolean'],
        ]);

        $preferences = $request->user()->notificationPreference ?? $request->user()->notificationPreference()->create();

        $preferences->update([
            'email_bookings' => $request->boolean('email_bookings'),
            'email_messages' => $request->boolean('email_messages'),
            'email_reminders' => $request->boolean('email_reminders'),
            'email_marketing' => $request->boolean('email_marketing'),
        ]);

        return back()->with('status', 'Notification preferences updated.');
    }
}
