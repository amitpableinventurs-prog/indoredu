<?php

namespace App\Policies;

use App\Models\Course;
use App\Models\User;

class CoursePolicy
{
    public function view(?User $user, Course $course): bool
    {
        if ($course->isPublished()) {
            return true;
        }

        return $user && ($user->isAdmin() || $user->id === $course->tutorProfile->user_id);
    }

    public function update(User $user, Course $course): bool
    {
        return $user->isAdmin() || $user->id === $course->tutorProfile->user_id;
    }

    public function delete(User $user, Course $course): bool
    {
        return $this->update($user, $course);
    }
}
