<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotificationPreference extends Model
{
    protected $fillable = [
        'user_id', 'email_bookings', 'email_messages', 'email_reminders', 'email_marketing',
    ];

    protected function casts(): array
    {
        return [
            'email_bookings' => 'boolean',
            'email_messages' => 'boolean',
            'email_reminders' => 'boolean',
            'email_marketing' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
