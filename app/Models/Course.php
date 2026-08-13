<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Course extends Model
{
    use HasFactory;

    const STATUS_DRAFT = 'draft';
    const STATUS_PENDING = 'pending';
    const STATUS_PUBLISHED = 'published';
    const STATUS_REJECTED = 'rejected';
    const STATUS_ARCHIVED = 'archived';

    const LEVEL_BEGINNER = 'beginner';
    const LEVEL_INTERMEDIATE = 'intermediate';
    const LEVEL_ADVANCED = 'advanced';
    const LEVEL_CRASH_COURSE = 'crash_course';
    const LEVEL_ALL_LEVELS = 'all_levels';

    const LEVELS = [
        self::LEVEL_BEGINNER => 'Beginner',
        self::LEVEL_INTERMEDIATE => 'Intermediate',
        self::LEVEL_ADVANCED => 'Advanced',
        self::LEVEL_CRASH_COURSE => 'Crash Course',
        self::LEVEL_ALL_LEVELS => 'All levels',
    ];

    const GRADE_ALL_GRADES = 'all_grades';

    const GRADES = [
        self::GRADE_ALL_GRADES => 'All grades',
        'class_1' => 'Class 1',
        'class_2' => 'Class 2',
        'class_3' => 'Class 3',
        'class_4' => 'Class 4',
        'class_5' => 'Class 5',
        'class_6' => 'Class 6',
        'class_7' => 'Class 7',
        'class_8' => 'Class 8',
        'class_9' => 'Class 9',
        'class_10' => 'Class 10',
        'class_11' => 'Class 11',
        'class_12' => 'Class 12',
        'undergraduate' => 'Undergraduate',
        'postgraduate' => 'Postgraduate',
    ];

    protected $fillable = [
        'tutor_profile_id', 'subject_id', 'title', 'slug', 'description',
        'level', 'grade', 'price', 'duration_minutes', 'total_sessions', 'is_group',
        'max_students', 'cover_image', 'status', 'rejection_reason',
        'rating_avg', 'rating_count',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'is_group' => 'boolean',
            'rating_avg' => 'decimal:2',
        ];
    }

    public function tutorProfile(): BelongsTo
    {
        return $this->belongsTo(TutorProfile::class);
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(CourseEnrollment::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(CourseReview::class);
    }

    public function isPublished(): bool
    {
        return $this->status === self::STATUS_PUBLISHED;
    }

    public function isCrashCourse(): bool
    {
        return $this->level === self::LEVEL_CRASH_COURSE;
    }

    protected function levelLabel(): Attribute
    {
        return Attribute::get(fn () => self::LEVELS[$this->level] ?? ucfirst(str_replace('_', ' ', $this->level)));
    }

    protected function gradeLabel(): Attribute
    {
        return Attribute::get(fn () => self::GRADES[$this->grade] ?? ucfirst(str_replace('_', ' ', $this->grade)));
    }
}
