<?php

namespace App\Policies;

use App\Models\Post;
use App\Models\User;

class PostPolicy
{
    /**
     * Kullanıcı gönderiyi görüntüleyebilir mi?
     */
    public function view(User $user, Post $post): bool
    {
        return $user->id === $post->user_id;
    }

    /**
     * Kullanıcı gönderiyi güncelleyebilir mi?
     */
    public function update(User $user, Post $post): bool
    {
        return $user->id === $post->user_id;
    }

    /**
     * Kullanıcı gönderiyi silebilir mi?
     */
    public function delete(User $user, Post $post): bool
    {
        return $user->id === $post->user_id;
    }
}
