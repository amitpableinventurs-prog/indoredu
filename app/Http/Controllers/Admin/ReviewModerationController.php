<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminActionLog;
use App\Models\Review;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReviewModerationController extends Controller
{
    public function index(Request $request): View
    {
        $filter = $request->query('filter', 'flagged');

        $reviews = Review::with(['student', 'tutor'])
            ->when($filter === 'flagged', fn ($q) => $q->where('is_flagged', true))
            ->when($filter === 'hidden', fn ($q) => $q->where('is_approved', false))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.reviews.index', compact('reviews', 'filter'));
    }

    public function toggleVisibility(Request $request, Review $review): RedirectResponse
    {
        $review->update(['is_approved' => ! $review->is_approved, 'is_flagged' => false]);
        AdminActionLog::record($request->user(), $review->is_approved ? 'review.restored' : 'review.hidden', $review);

        return back()->with('status', 'Review updated.');
    }

    public function destroy(Request $request, Review $review): RedirectResponse
    {
        AdminActionLog::record($request->user(), 'review.deleted', $review, ['rating' => $review->rating]);
        $review->delete();

        return back()->with('status', 'Review deleted.');
    }
}
