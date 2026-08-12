<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends Model
{
    protected $fillable = [
        'booking_id', 'student_status', 'tutor_status',
        'student_check_in', 'student_check_out',
        'tutor_check_in', 'tutor_check_out', 'marked_by', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'student_check_in' => 'datetime',
            'student_check_out' => 'datetime',
            'tutor_check_in' => 'datetime',
            'tutor_check_out' => 'datetime',
        ];
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function markedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'marked_by');
    }
}
