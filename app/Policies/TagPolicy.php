<?php

namespace App\Policies;

use App\Models\Tag;
use App\Models\User;

class TagPolicy
{
    /**
     * Kullanıcı kendi etiketlerini güncelleyebilir.
     */
    public function update(User $user, Tag $tag): bool
    {
        return $user->id === $tag->user_id;
    }

    /**
     * Kullanıcı kendi etiketlerini silebilir.
     */
    public function delete(User $user, Tag $tag): bool
    {
        return $user->id === $tag->user_id;
    }

    /**
     * Kullanıcı kendi etiketini görüntüleyebilir.
     */
    public function view(User $user, Tag $tag): bool
    {
        return $user->id === $tag->user_id;
    }
}
