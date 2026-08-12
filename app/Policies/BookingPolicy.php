<?php

namespace App\Policies;

use App\Models\Booking;
use App\Models\User;

class BookingPolicy
{
    public function view(User $user, Booking $booking): bool
    {
        return $user->isAdmin()
            || $user->id === $booking->student_id
            || $user->id === $booking->tutor_id;
    }

    public function update(User $user, Booking $booking): bool
    {
        return $user->isAdmin()
            || $user->id === $booking->student_id
            || $user->id === $booking->tutor_id;
    }

    public function cancel(User $user, Booking $booking): bool
    {
        return $this->update($user, $booking);
    }

    public function markAttendance(User $user, Booking $booking): bool
    {
        return $user->isAdmin() || $user->id === $booking->tutor_id;
    }
}
