<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TutorTimeOff extends Model
{
    protected $fillable = ['tutor_profile_id', 'date', 'start_time', 'end_time', 'reason'];

    protected function casts(): array
    {
        return [
            'date' => 'date',
        ];
    }

    public function tutorProfile(): BelongsTo
    {
        return $this->belongsTo(TutorProfile::class);
    }
}
