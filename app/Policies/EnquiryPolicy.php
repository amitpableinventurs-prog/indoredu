<?php

namespace App\Policies;

use App\Models\Enquiry;
use App\Models\User;

class EnquiryPolicy
{
    public function view(User $user, Enquiry $enquiry): bool
    {
        return $user->isAdmin() || $enquiry->involves($user);
    }

    public function reply(User $user, Enquiry $enquiry): bool
    {
        return $user->id === $enquiry->tutor_id && $enquiry->status === Enquiry::STATUS_PENDING;
    }

    public function decline(User $user, Enquiry $enquiry): bool
    {
        return $user->id === $enquiry->tutor_id && $enquiry->status === Enquiry::STATUS_PENDING;
    }

    public function close(User $user, Enquiry $enquiry): bool
    {
        return $enquiry->involves($user) && $enquiry->isOpen();
    }
}
