<?php

namespace App\Policies;

use App\Models\Document;
use App\Models\User;

class DocumentPolicy
{
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
