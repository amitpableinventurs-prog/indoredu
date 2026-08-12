<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subject extends Model
{
    use HasFactory;

    protected $fillable = ['subject_category_id', 'name', 'slug', 'description', 'syllabus', 'syllabus_pdf', 'icon', 'is_active'];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(SubjectCategory::class, 'subject_category_id');
    }

    public function tutorProfiles(): BelongsToMany
    {
        return $this->belongsToMany(TutorProfile::class, 'tutor_subjects')
            ->withPivot(['level', 'hourly_rate'])
            ->withTimestamps();
    }

    public function courses(): HasMany
    {
        return $this->hasMany(Course::class);
    }
}
