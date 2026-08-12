<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;

class DashboardController extends Controller
{
    public function index(): RedirectResponse
    {
        return match (auth()->user()->role) {
            User::ROLE_ADMIN => redirect()->route('admin.dashboard'),
            User::ROLE_TUTOR => redirect()->route('tutor.dashboard'),
            default => redirect()->route('student.dashboard'),
        };
    }
}
