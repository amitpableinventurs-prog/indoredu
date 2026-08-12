<?php

namespace App\Policies;

use App\Models\Review;
use App\Models\User;

class ReviewPolicy
{
    public function update(User $user, Review $review): bool
    {
        return $user->id === $review->student_id;
    }

    public function respond(User $user, Review $review): bool
    {
        return $user->id === $review->tutor_id;
    }

    public function delete(User $user, Review $review): bool
    {
        return $user->isAdmin() || $user->id === $review->student_id;
    }
}
