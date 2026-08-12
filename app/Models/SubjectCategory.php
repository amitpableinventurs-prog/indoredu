<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SubjectCategory extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'icon', 'board', 'university', 'education_level'];

    public function subjects(): HasMany
    {
        return $this->hasMany(Subject::class);
    }
}
