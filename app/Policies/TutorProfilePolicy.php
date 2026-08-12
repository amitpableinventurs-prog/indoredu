<?php

namespace App\Policies;

use App\Models\TutorProfile;
use App\Models\User;

class TutorProfilePolicy
{
    public function update(User $user, TutorProfile $tutorProfile): bool
    {
        return $user->isAdmin() || $user->id === $tutorProfile->user_id;
    }
}
