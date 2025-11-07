<?php

namespace App\Policies;

use App\Models\Document;
use App\Models\User;

class DocumentPolicy
{
    public function viewAny(User $user, ?User $targetUser = null): bool
    {
        // Eğer başka bir kullanıcının belgeleri isteniyorsa, sadece kendisininki için izin ver
        if ($targetUser && $user->id !== $targetUser->id) {
            return false;
        }
        return true;
    }

    /**
     * Kullanıcı gönderiyi görüntüleyebilir mi?
     */
    public function view(User $user, Document $document): bool
    {
        return $user->id === $document->user_id;
    }

    /**
     * Kullanıcı gönderiyi güncelleyebilir mi?
     */
    public function update(User $user, Document $document): bool
    {
        return $user->id === $document->user_id;
    }

    /**
     * Kullanıcı gönderiyi silebilir mi?
     */
    public function delete(User $user, Document $document): bool
    {
        return $user->id === $document->user_id;
    }
}
