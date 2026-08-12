<?php

namespace App\Policies;

use App\Models\Conversation;
use App\Models\User;

class ConversationPolicy
{
    public function view(User $user, Conversation $conversation): bool
    {
        return $user->isAdmin() || $conversation->participants->contains('id', $user->id);
    }

    public function sendMessage(User $user, Conversation $conversation): bool
    {
        return $conversation->participants->contains('id', $user->id);
    }
}
