<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TutorProfile extends Model
{
    use HasFactory;

    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';
    const STATUS_SUSPENDED = 'suspended';

    protected $fillable = [
        'user_id', 'headline', 'bio', 'hourly_rate', 'currency',
        'offers_trial', 'trial_price',
        'experience_years', 'education', 'video_intro_url', 'languages',
        'identity_document', 'status', 'rejection_reason', 'is_featured',
        'rating_avg', 'rating_count', 'total_sessions', 'total_students',
        'approved_at', 'approved_by',
    ];

    protected function casts(): array
    {
        return [
            'languages' => 'array',
            'is_featured' => 'boolean',
            'offers_trial' => 'boolean',
            'hourly_rate' => 'decimal:2',
            'trial_price' => 'decimal:2',
            'rating_avg' => 'decimal:2',
            'approved_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function subjects(): BelongsToMany
    {
        return $this->belongsToMany(Subject::class, 'tutor_subjects')
            ->withPivot(['level', 'hourly_rate'])
            ->withTimestamps();
    }

    public function tutorSubjects(): HasMany
    {
        return $this->hasMany(TutorSubject::class);
    }

    public function availabilities(): HasMany
    {
        return $this->hasMany(TutorAvailability::class);
    }

    public function timeOffs(): HasMany
    {
        return $this->hasMany(TutorTimeOff::class);
    }

    public function courses(): HasMany
    {
        return $this->hasMany(Course::class);
    }

    public function payouts(): HasMany
    {
        return $this->hasMany(Payout::class);
    }

    public function certificates(): HasMany
    {
        return $this->hasMany(TutorCertificate::class);
    }

    public function isApproved(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }
}
