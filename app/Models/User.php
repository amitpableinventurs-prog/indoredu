<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

    const ROLE_ADMIN = 'admin';
    const ROLE_TUTOR = 'tutor';
    const ROLE_STUDENT = 'student';

    const STATUS_ACTIVE = 'active';
    const STATUS_SUSPENDED = 'suspended';
    const STATUS_PENDING = 'pending';
    const STATUS_BANNED = 'banned';

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'status',
        'phone',
        'avatar',
        'timezone',
        'city',
        'country',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_secret',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'password' => 'hashed',
            'two_factor_enabled' => 'boolean',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isTutor(): bool
    {
        return $this->role === self::ROLE_TUTOR;
    }

    public function isStudent(): bool
    {
        return $this->role === self::ROLE_STUDENT;
    }

    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    public function tutorProfile(): HasOne
    {
        return $this->hasOne(TutorProfile::class);
    }

    public function studentProfile(): HasOne
    {
        return $this->hasOne(StudentProfile::class);
    }

    public function bookingsAsStudent(): HasMany
    {
        return $this->hasMany(Booking::class, 'student_id');
    }

    public function bookingsAsTutor(): HasMany
    {
        return $this->hasMany(Booking::class, 'tutor_id');
    }

    public function sentMessages(): HasMany
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    public function conversations(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Conversation::class, 'conversation_participants')
            ->withPivot('last_read_at')
            ->withTimestamps();
    }

    public function unreadMessagesCount(): int
    {
        return $this->conversations()
            ->where(function ($q) {
                $q->whereNull('conversation_participants.last_read_at')
                    ->orWhereColumn('conversations.last_message_at', '>', 'conversation_participants.last_read_at');
            })
            ->whereNotNull('conversations.last_message_at')
            ->count();
    }

    public function paymentsMade(): HasMany
    {
        return $this->hasMany(Payment::class, 'payer_id');
    }

    public function paymentsReceived(): HasMany
    {
        return $this->hasMany(Payment::class, 'payee_id');
    }

    public function notificationPreference(): HasOne
    {
        return $this->hasOne(NotificationPreference::class);
    }

    public function contentReportsFiled(): HasMany
    {
        return $this->hasMany(ContentReport::class, 'reporter_id');
    }

    public function blockedUsers(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_blocks', 'blocker_id', 'blocked_id');
    }

    public function hasBlocked(User $user): bool
    {
        return UserBlock::where('blocker_id', $this->id)->where('blocked_id', $user->id)->exists();
    }

    public function isBlockedBy(User $user): bool
    {
        return UserBlock::where('blocker_id', $user->id)->where('blocked_id', $this->id)->exists();
    }

    public function initials(): string
    {
        $words = explode(' ', trim($this->name));
        $initials = array_map(fn ($w) => mb_strtoupper(mb_substr($w, 0, 1)), array_slice($words, 0, 2));

        return implode('', $initials) ?: '?';
    }
}
