<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Enquiry extends Model
{
    const STATUS_PENDING = 'pending';
    const STATUS_REPLIED = 'replied';
    const STATUS_DECLINED = 'declined';
    const STATUS_CLOSED = 'closed';

    const MODES = [
        'online' => 'Online',
        'offline' => 'In person',
        'either' => 'Either',
    ];

    protected $fillable = [
        'student_id', 'tutor_id', 'subject_id', 'course_id', 'conversation_id',
        'title', 'message', 'grade', 'preferred_mode', 'preferred_time',
        'status', 'tutor_reply', 'replied_at', 'closed_at',
    ];

    protected function casts(): array
    {
        return [
            'replied_at' => 'datetime',
            'closed_at' => 'datetime',
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

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class);
    }

    public function isOpen(): bool
    {
        return in_array($this->status, [self::STATUS_PENDING, self::STATUS_REPLIED], true);
    }

    public function involves(User $user): bool
    {
        return $user->id === $this->student_id || $user->id === $this->tutor_id;
    }
}
