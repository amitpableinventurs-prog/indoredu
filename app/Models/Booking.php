<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Booking extends Model
{
    use HasFactory;

    const STATUS_PENDING = 'pending';
    const STATUS_CONFIRMED = 'confirmed';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_NO_SHOW = 'no_show';

    protected $fillable = [
        'student_id', 'tutor_id', 'subject_id', 'course_id',
        'scheduled_date', 'start_time', 'end_time', 'timezone',
        'duration_minutes', 'is_trial', 'price', 'status', 'meeting_link',
        'student_notes', 'tutor_notes', 'cancelled_by',
        'cancellation_reason', 'cancelled_at', 'confirmed_at',
        'completed_at', 'reminder_sent_at',
    ];

    protected function casts(): array
    {
        return [
            'scheduled_date' => 'date',
            'price' => 'decimal:2',
            'is_trial' => 'boolean',
            'cancelled_at' => 'datetime',
            'confirmed_at' => 'datetime',
            'completed_at' => 'datetime',
            'reminder_sent_at' => 'datetime',
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function tutor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'tutor_id');
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function attendance(): HasOne
    {
        return $this->hasOne(Attendance::class);
    }

    public function review(): HasOne
    {
        return $this->hasOne(Review::class);
    }

    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }

    public function conversation(): HasOne
    {
        return $this->hasOne(Conversation::class);
    }

    public function isUpcoming(): bool
    {
        return in_array($this->status, [self::STATUS_PENDING, self::STATUS_CONFIRMED])
            && $this->scheduled_date->isFuture();
    }
}
