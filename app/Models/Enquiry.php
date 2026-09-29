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

    /** Common questions a student can tick; the tutor answers each one. */
    const QUESTIONS = [
        'fees' => 'What is your fee (per class / per month)?',
        'trial' => 'Do you offer a free trial or demo class?',
        'timings' => 'What are your available timings?',
        'board' => 'Which boards / syllabus do you teach (CBSE, ICSE, State)?',
        'mode' => 'Do you teach online, at home, or both?',
        'batch' => 'Is it one-to-one or group / batch classes?',
        'material' => 'Do you provide notes, study material or test series?',
        'doubts' => 'Can I ask doubts outside class time?',
        'progress' => 'How do you track progress and update parents?',
        'experience' => 'How much experience do you have with this class / subject?',
    ];

    protected $fillable = [
        'student_id', 'tutor_id', 'subject_id', 'course_id', 'conversation_id',
        'title', 'questions', 'message', 'grade', 'preferred_mode', 'preferred_time',
        'status', 'tutor_reply', 'answers', 'replied_at', 'closed_at',
    ];

    protected function casts(): array
    {
        return [
            'questions' => 'array',
            'answers' => 'array',
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

    /**
     * The ticked questions as [key => label], skipping keys no longer offered.
     */
    public function questionList(): array
    {
        return collect($this->questions ?? [])
            ->filter(fn ($key) => isset(self::QUESTIONS[$key]))
            ->mapWithKeys(fn ($key) => [$key => self::QUESTIONS[$key]])
            ->all();
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
