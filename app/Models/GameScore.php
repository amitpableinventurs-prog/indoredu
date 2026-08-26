<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GameScore extends Model
{
    protected $fillable = [
        'user_id',
        'game',
        'best_score',
        'plays',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
